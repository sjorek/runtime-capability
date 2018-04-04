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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;
use Sjorek\RuntimeCapability\Iteration\GlobFilterKeyIterator;
use Sjorek\RuntimeCapability\Iteration\FilesystemFilterIterator;
use Sjorek\RuntimeCapability\Filesystem\Driver\FileTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\DirectoryTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetDriverInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PHPFilesystemDirectoryDriver extends AbstractPHPFilesystemDriver implements FilesystemDirectoryDriverInterface
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
     * @param PHPFilesystemDriverInterface $targetDriver
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(PHPFilesystemDriverInterface $targetDriver = null)
    {
        if ($targetDriver instanceof FilesystemDirectoryDriverInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The driver must not implement the interface: %s',
                    FilesystemDirectoryDriverInterface::class
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
     * @see FilesystemDirectoryDriverInterface::getDirectory()
     */
    public function getDirectory()
    {
        return $this->getWorkingDirectory();
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDirectoryDriverInterface::setDirectory()
     */
    public function setDirectory($path = null)
    {
        if (null === $path) {
            $path = parent::normalizePath($this->getWorkingDirectory());
        } else {
            $path = $this->normalizePath($path);
        }

        if (!FilesystemUtility::isDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory does not exist.',
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
                    'Invalid path given: %s. The directory accessible (executable).',
                    $path
                ),
                1522171549
            );
        }

        return $this->setWorkingDirectory($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDirectoryDriverInterface::setIteratorPattern()
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
     * @param string path
     *
     * @return string
     */
    protected function prependDirectory(string $path): string
    {
        $this->validatePath($path);

        if (FilesystemUtility::isAbsolutePath($path) || FilesystemUtility::isUrl($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Can not prepend directory to absolute paths or urls.', $path),
                1522171647
            );
        }

        if ('.' === $path[0]) {
            $path = substr($path, 1);
        }

        $path = ltrim($path, '/\\');
        if ('' === $path) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Can not prepend directory to empty paths.', $path),
                1522318072
            );
        }

        return $this->getDirectory() . '/' . $path;
    }

    /**
     * @param string $directory
     * @param string $pattern
     * @return \Iterator
     */
    protected function createFilesystemIterator(string $directory, string $pattern): \Iterator
    {
        $iterator = new \FilesystemIterator($directory, self::FILESYSTEM_ITERATOR_FLAGS);

        $filterFlags = 0;
        if ($this->targetDriver instanceof FileTargetDriverInterface) {
            $filterFlags |= FilesystemFilterIterator::ACCEPT_FILE;
        }
        if ($this->targetDriver instanceof DirectoryTargetDriverInterface) {
            $filterFlags |= FilesystemFilterIterator::ACCEPT_DIRECTORY;
        }
        if ($this->targetDriver instanceof LinkTargetDriverInterface) {
            $filterFlags |= FilesystemFilterIterator::ACCEPT_LINK;
        }
        if (0 === $filterFlags) {
            $filterFlags = FilesystemFilterIterator::DEFAULT_FLAGS;
        }
        $iterator = new FilesystemFilterIterator($iterator, $filterFlags);

        $iterator = new GlobFilterKeyIterator($iterator, $pattern, self::FNMATCH_PATTERN_FLAGS);

        return $iterator;
    }
}
