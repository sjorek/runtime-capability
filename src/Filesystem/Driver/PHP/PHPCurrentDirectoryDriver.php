<?php

declare(strict_types=1);

/*
 * This file is part of the Runtime Capability project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\CurrentDirectoryStrategyInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\DirectoryTargetInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\FileTargetInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\LinkTargetInterface;
use Sjorek\RuntimeCapability\Iteration\FilesystemFilterByTypeIterator;
use Sjorek\RuntimeCapability\Iteration\GlobFilterKeyIterator;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PHPCurrentDirectoryDriver extends AbstractPHPFilesystemDriver implements CurrentDirectoryStrategyInterface
{
    /**
     * @var int
     */
    const FILESYSTEM_ITERATOR_FLAGS =
        \FilesystemIterator::KEY_AS_PATHNAME |
        \FilesystemIterator::CURRENT_AS_FILEINFO |
        \FilesystemIterator::SKIP_DOTS |
        \FilesystemIterator::UNIX_PATHS
    ;

    /**
     * @var int
     */
    const FNMATCH_PATTERN_FLAGS =
        FNM_PATHNAME |
        FNM_PERIOD |
        FNM_CASEFOLD
    ;

    /**
     * @var PHPFilesystemDriverInterface
     */
    protected $targetDriver;

    /**
     * @var string
     */
    protected $iteratorPattern = '*';

    /**
     * @var string
     */
    protected $workingDirectory = null;

    /**
     * @param null|PHPFilesystemDriverInterface $targetDriver
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(PHPFilesystemDriverInterface $targetDriver = null)
    {
        if (null === $targetDriver) {
            $targetDriver = new FileTargetDriver();
        }
        if ($targetDriver instanceof CurrentDirectoryStrategyInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The driver must not implement the interface: %s',
                    CurrentDirectoryStrategyInterface::class
                ),
                1522331750
            );
        }
        $this->targetDriver = $targetDriver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::createTarget()
     */
    public function createTarget($path): bool
    {
        // $this->diver->setWorkingDirectory($this->getWorkingDirectory());

        return $this->targetDriver->createTarget($this->prependDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::targetExists()
     */
    public function targetExists($path): bool
    {
        // $this->diver->setWorkingDirectory($this->getWorkingDirectory());

        return $this->targetDriver->targetExists($this->prependDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::removeTarget()
     */
    public function removeTarget($path): bool
    {
        // $this->diver->setWorkingDirectory($this->getWorkingDirectory());

        return $this->targetDriver->removeTarget($this->prependDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Strategy\CurrentDirectoryStrategyInterface::getDirectory()
     */
    public function getDirectory()
    {
        return $this->getWorkingDirectory();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Strategy\CurrentDirectoryStrategyInterface::setIteratorPattern()
     */
    public function setIteratorPattern($pattern)
    {
        $this->iteratorPattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        $directory = $this->getDirectory();
        $strip = strlen($directory) + 1;
        $iterator = $this->createFilesystemIterator($directory, $this->normalizePath($this->iteratorPattern));

        $generator = function () use ($iterator, $strip) {
            foreach ($iterator as $path => $entry) {
                yield substr($path, $strip) => $entry;
            }
        };

        return $generator();
    }

    /**
     * Normalize the given path and validate the normalized path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  CWD             # replace single dot with current working directory
     * ./test       =>  CWD/test        # replace leading dot with current working directory in path
     * .\test       =>  CWD/test        # replace leading dot with current working directory in windows path
     * test/file    =>  CWD/test/file   # prepend current working directory to relative path
     * test\file    =>  CWD/test/file   # prepend current working directory to relative windows path
     * /test        =>  /test           # keep absolute posix path absolute
     * c:\test      =>  c:/test         # keep absolute windows path absolute
     * </pre>
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        $path = parent::normalizePath($path);

        if (!(FilesystemUtility::isAbsolutePath($path) || FilesystemUtility::isUrl($path))) {
            if ('' === $path) {
                $path = $this->getWorkingDirectory();
            } else {
                $path = $this->getWorkingDirectory() . '/' . $path;
            }
        }

        $this->validatePath($path);

        return $path;
    }

    /**
     * Return the current working directory.
     *
     * @return string
     *
     * @see FilesystemUtility::getWorkingDirectory()
     */
    protected function getWorkingDirectory(): string
    {
        if (null !== $this->workingDirectory) {
            return $this->workingDirectory;
        }

        return $this->setWorkingDirectory(FilesystemUtility::getCurrentWorkingDirectory());
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function setWorkingDirectory(string $path): string
    {
        $path = $this->normalizePath($path);

        if (!FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The path does not exist.',
                    $path
                ),
                1522171541
            );
        }

        if (!FilesystemUtility::isDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The path is not a directory.',
                    $path
                ),
                1522171543
            );
        }

        if (!FilesystemUtility::isWritableDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory is not writable.',
                    $path
                ),
                1522171546
            );
        }

        // any existence-checks are reliable in accessible (executable) directories only
        if (!FilesystemUtility::isExecutableDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory is not accessible (executable).',
                    $path
                ),
                1522171549
            );
        }

        return $this->workingDirectory = $path;
    }

    /**
     * @param string path
     *
     * @return string
     */
    protected function prependDirectory(string $path): string
    {
        $this->validatePath($path);

        if (FilesystemUtility::isAbsolutePath($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Can not prepend directory to an absolute path.', $path),
                1522171647
            );
        }

        if (FilesystemUtility::isUrl($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Can not prepend directory to an url.', $path),
                1522171650
            );
        }

        if ('.' === $path[0]) {
            $path = substr($path, 1);
        }

        $path = ltrim($path, '/\\');
        if ('' === $path) {
            throw new \InvalidArgumentException(
                'Invalid path given. Can not prepend directory to current directory (.) without any path.',
                1522318072
            );
        }

        return $this->getDirectory() . '/' . $path;
    }

    /**
     * @param string $directory
     * @param string $pattern
     *
     * @return GlobFilterKeyIterator
     */
    protected function createFilesystemIterator(string $directory, string $pattern): GlobFilterKeyIterator
    {
        $iterator = new \FilesystemIterator($directory, self::FILESYSTEM_ITERATOR_FLAGS);

        $flags = FilesystemFilterByTypeIterator::ACCEPT_NONE;
        if ($this->targetDriver instanceof FileTargetInterface) {
            $flags |= FilesystemFilterByTypeIterator::ACCEPT_FILE;
        }
        if ($this->targetDriver instanceof DirectoryTargetInterface) {
            $flags |= FilesystemFilterByTypeIterator::ACCEPT_DIRECTORY;
        }
        if ($this->targetDriver instanceof LinkTargetInterface) {
            $flags |= FilesystemFilterByTypeIterator::ACCEPT_LINK;
        }
        if (FilesystemFilterByTypeIterator::ACCEPT_NONE !== $flags) {
            $iterator = new FilesystemFilterByTypeIterator($iterator, $flags);
        }

        return new GlobFilterKeyIterator($iterator, $pattern, self::FNMATCH_PATTERN_FLAGS);
    }
}
