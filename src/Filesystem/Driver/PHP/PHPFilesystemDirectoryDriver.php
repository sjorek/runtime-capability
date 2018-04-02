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

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PHPFilesystemDirectoryDriver extends AbstractPHPFilesystemDriver implements FilesystemDirectoryDriverInterface
{
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

        return $this->targetDriver->createTarget($this->prependWorkingDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::targetExists()
     */
    public function targetExists($path): bool
    {
        // $this->diver->setWorkingDirectory($this->getWorkingDirectory());

        return $this->targetDriver->targetExists($this->prependWorkingDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::removeTarget()
     */
    public function removeTarget($path): bool
    {
        // $this->diver->setWorkingDirectory($this->getWorkingDirectory());

        return $this->targetDriver->removeTarget($this->prependWorkingDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDirectoryDriverInterface::getWorkingDirectory()
     */
    public function getWorkingDirectory()
    {
        return parent::getWorkingDirectory();
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDirectoryDriverInterface::setWorkingDirectory()
     */
    public function setWorkingDirectory($path = null)
    {
        if (null === $path) {
            $path = parent::normalizePath($this->getWorkingDirectory());
        } else {
            $path = $this->normalizePath($path);
        }

        if (!FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory does not exist.',
                    $path
                ),
                1522171543
            );
        }

        // any existence-checks are reliable in accessible (executable) directories only
        if (!FilesystemUtility::isAccessibleDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory accessible (executable).',
                    $path
                ),
                1522171546
            );
        }

        if (!FilesystemUtility::isWritableDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The directory is not writable.',
                    $path
                ),
                1522171549
            );
        }

        return $this->workingDirectory = $path;
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
        $pattern = $this->normalizePath($this->iteratorPattern);
        $strip = strlen($this->workingDirectory) + 1;
        $generator = function () use ($pattern, $strip) {
            foreach (glob($pattern, GLOB_NOSORT) as $path) {
                yield substr($path, $strip);
            }
        };

        return $generator();
    }

    /**
     * @param string path
     *
     * @return string
     */
    protected function prependWorkingDirectory(string $path): string
    {
        $this->validatePath($path);

        if (FilesystemUtility::isAbsolutePath($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Can not prepend directory to absolute paths.', $path),
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

        return $this->workingDirectory . '/' . $path;
    }
}
