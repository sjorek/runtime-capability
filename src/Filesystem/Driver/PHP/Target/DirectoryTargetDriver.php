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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDirectoryTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class DirectoryTargetDriver extends AbstractPHPFilesystemDriver implements FilesystemDirectoryTargetDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::createTarget()
     */
    public function createTarget($path): bool
    {
        return $this->createDirectory((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::targetExists()
     */
    public function targetExists($path): bool
    {
        return $this->directoryExists((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::removeTarget()
     */
    public function removeTarget($path): bool
    {
        return $this->removeDirectory((string) $path);
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException if the given path already exists
     * @throws \RuntimeException         on file creation failure
     *
     * @return bool
     */
    protected function createDirectory(string $path): bool
    {
        $path = $this->normalizePath($path);

        if (FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf('The path already exists: %s.', $path),
                1522314570
            );
        }

        if (false === @mkdir($path)) {
            if (false === ($message = error_get_last()['message'] ?? false)) {
                $message = $path;
            } else {
                error_clear_last();
            }
            throw new \RuntimeException(
                sprintf('Failed to create directory: %s', $message),
                1522314576
            );
        }

        return true;
    }

    /**
     * Attention: the existence-check is reliable in executable-directories only.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function directoryExists(string $path): bool
    {
        $path = $this->normalizePath($path);
        FilesystemUtility::cleanup($path);

        return FilesystemUtility::isDirectory($path);
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException if the given path does not exist or is not file
     * @throws \RuntimeException         on file removal failure
     *
     * @return bool
     */
    protected function removeDirectory(string $path): bool
    {
        $path = $this->normalizePath($path);

        if (!FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf('The file does not exist: %s.', $path),
                1522314670
            );
        }

        if (!FilesystemUtility::isDirectory($path)) {
            throw new \InvalidArgumentException(
                sprintf('The given path is not a directory: %s.', $path),
                1522314673
            );
        }

        if (false === @rmdir($path)) {
            if (false === ($message = error_get_last()['message'] ?? false)) {
                $message = $path;
            } else {
                error_clear_last();
            }
            throw new \RuntimeException(
                sprintf('Failed to remove directory: %s', $message),
                1522314676
            );
        }
        FilesystemUtility::cleanup($path);

        return true;
    }
}
