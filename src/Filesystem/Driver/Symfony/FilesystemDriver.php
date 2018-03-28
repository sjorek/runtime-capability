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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\Symfony;

use Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDriver extends AbstractFilesystemDriver implements SymfonyDrivenFilesystemDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::create()
     */
    public function create($path)
    {
        $path = $this->normalizePath($path);
        if ('' === $path || $this->exists($path) ||
            // the existence-check above is reliable in executable-directories only
            !($this->hasExecutableParentDirectory($path) && $this->hasWritableParentDirectory($path))
        ) {
            return false;
        }

        return touch($path);
    }

    /**
     * Attention: the existence-check is reliable in executable-directories only
     *
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::exists()
     */
    public function exists($path)
    {
        $path = $this->normalizePath($path);

        // the additional is_link() check ensures capturing existing dangling symlinks
        return '' !== $path && (file_exists($path) || is_link($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        $path = $this->normalizePath($path);
        if ($this->exists($path) && is_file($path) && $this->hasWritableParentDirectory($path)) {
            return unlink($path);
        }

        return false;
    }

    /**
     * Normalize the given path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  CWD             # replace single dot with current working directory
     * ./test       =>  CWD/test        # replace leading dot with current working directory
     * .\test       =>  CWD/test        # turn windows backslash into slash
     * test/file    =>  test/file       # keep path without leading dot relative
     * test\file    =>  test/file       # keep windows path without leading dot relative, still converting to slash
     * </pre>
     *
     * @param string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        $path = (string) $path;

        if ('' === $path) {
            return $path;
        }

        $path = strtr($path, '\\', '/');

        if (('.' === $path || (1 < strlen($path) && '.' === $path[0] && '/' === $path[1])) &&
            false !== ($cwd = getcwd()))
        {
            return $cwd . substr($path, 1);
        }

        return $path;
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function hasExecutableParentDirectory($path)
    {
        $path = dirname($path);

        return
            is_dir($path) &&
            (
                // TODO Find out why is_executable() fails for some vfs-directories
                is_executable($path) ||
                (
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    ($perms = (@fileperms($path) ?: 0)) &&
                    (
                        ($perms & 0x0040) && !($perms & 0x0800) || // owner executable flag - [u]ser
                        ($perms & 0x0008) && !($perms & 0x0400) || // group executable flag - [g]roup
                        ($perms & 0x0001) && !($perms & 0x0200)    // world executable flag - [o]ther
                    )
                )
            )
        ;
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function hasWritableParentDirectory($path)
    {
        $path = dirname($path);

        return is_dir($path) && is_writable($path);
    }
}
