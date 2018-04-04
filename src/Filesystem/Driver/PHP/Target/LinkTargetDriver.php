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

use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class LinkTargetDriver extends AbstractPHPFilesystemDriver implements LinkTargetDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::createTarget()
     */
    public function createTarget($path): bool
    {
        return $this->createSymbolicLink((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::targetExists()
     */
    public function targetExists($path): bool
    {
        return $this->symbolicLinkExists((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::removeTarget()
     */
    public function removeTarget($path): bool
    {
        return $this->removeSymbolicLink((string) $path);
    }

    /**
     * Create a symlink.
     *
     * @param string $path path of symlink to create or just its name
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return bool
     */
    protected function createSymbolicLink(string $path): bool
    {
        $path = $this->normalizePath($path);

        if (FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf('The path already exists: %s.', $path),
                1522314570
            );
        }

        if (false === @symlink(static::SYMBOLIC_LINK_TARGET, $path)) {
            if (false === ($message = error_get_last()['message'] ?? false)) {
                $message = $path;
            } else {
                error_clear_last();
            }
            throw new \RuntimeException(
                sprintf('Failed to create symlink %s: %s', $path, $message),
                1522314776
            );
        }

        return true;
    }

    /**
     * Check for symlink existence - which means only symlink.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function symbolicLinkExists(string $path): bool
    {
        $path = $this->normalizePath($path);
        FilesystemUtility::cleanup($path);

        return FilesystemUtility::isSymbolicLink($path); // && static::SYMBOLIC_LINK_TARGET === readlink($path);
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException if the given path does not exist or is not file
     * @throws \RuntimeException         on file removal failure
     *
     * @return bool
     */
    protected function removeSymbolicLink(string $path): bool
    {
        $path = $this->normalizePath($path);

        if (!FilesystemUtility::pathExists($path)) {
            throw new \InvalidArgumentException(
                sprintf('The symlink does not exist: %s.', $path),
                1522314870
            );
        }

        if (!FilesystemUtility::isSymbolicLink($path)) {
            throw new \InvalidArgumentException(
                sprintf('The given path is not a symlink: %s.', $path),
                1522314873
            );
        }

        if (false === @unlink($path)) {
            if (false === ($message = error_get_last()['message'] ?? false)) {
                $message = $path;
            } else {
                error_clear_last();
            }
            throw new \RuntimeException(
                sprintf('Failed to remove symlink: %s', $message),
                1522314876
            );
        }
        FilesystemUtility::cleanup($path);

        return true;
    }
}
