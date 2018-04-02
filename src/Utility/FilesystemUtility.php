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

namespace Sjorek\RuntimeCapability\Utility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
final class FilesystemUtility
{
    /**
     * Check for path existence, no matter which kind of entry the path points to.
     * Uses an additional is_link() check to ensure capturing existing dangling symlinks.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function pathExists(string $path): bool
    {
        return file_exists($path) || is_link($path);
    }

    /**
     * Check for file existence - which means only files.
     * Uses an additional is_link() check to exclude symlinks.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isFile(string $path): bool
    {
        return file_exists($path) && is_file($path) && !is_link($path);
    }

    /**
     * Check for file existence - which means only files.
     * Uses an additional is_link() check to exclude symlinks.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isDirectory(string $path): bool
    {
        return file_exists($path) && is_dir($path) && !is_link($path);
    }

    /**
     * Check for symbolic link existence - which means only files.
     * Uses an additional is_link() check to exclude symlinks.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isSymbolicLink(string $path): bool
    {
        return file_exists($path) && is_link($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isAccessibleDirectory(string $path): bool
    {
        return
            self::isDirectory($path) &&
            (
                // TODO Find out why is_executable() fails for some vfs-directories
                is_executable($path) ||
                (
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    ($permissions = (@fileperms($path) ?: 0)) &&
                    (
                        ($permissions & 0x0040) && !($permissions & 0x0800) || // owner executable flag - [u]ser
                        ($permissions & 0x0008) && !($permissions & 0x0400) || // group executable flag - [g]roup
                        ($permissions & 0x0001) && !($permissions & 0x0200)    // world executable flag - [o]ther
                    )
                )
            )
        ;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isWritableDirectory(string $path): bool
    {
        return self::isDirectory($path) && is_writable($path);
    }

    /**
     * Normalize the given path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  CWD             # replace single dot with current working directory
     * ./test       =>  CWD/test        # replace leading dot with current working directory in path
     * .\test       =>  CWD/test        # replace leading dot with current working directory in windows path
     * test/file    =>  CWD/test/file   # prepend current working directory to relative path
     * test\file    =>  CWD/test/file   # prepend current working directory to relative windows path
     * </pre>
     *
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath(string $path): string
    {
        if ('' === $path) {
            return $path;
        }

        $path = rtrim(strtr($path, '\\', '/'), '/');
        if ('' === $path) {
            $path = '/';
        }

        if (self::isAbsolutePath($path)) {
            // TODO throw exception for absolute paths during path normalization? I think so …
            return $path;
        }

        if ('.' === $path || (1 < strlen($path) && '.' === $path[0] && '/' === $path[1])) {
            $path = substr($path, 2);
        }

        return $path;
    }

    /**
     * Return the current working directory or '.' if the directory can't be determined.
     *
     * @return string
     */
    public static function getCurrentWorkingDirectory(): string
    {
        if (false === ($cwd = getcwd()) && false === ($cwd = realpath('.'))) {
            return '.';
        }
        $cwd = rtrim(strtr($cwd, '\\', '/'), '/');

        return '' !== $cwd ? $cwd : '/';
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolutePath(string $path): bool
    {
        if ('' === $path) {
            return false;
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            return in_array(['/', '\\'], $path[self::hasWindowsDrivePrefix($path) ? 2 : 0] ?? null, true);
        }

        return '/' === $path[0];
    }

    /**
     * Check if the path represents an url - a scheme is required.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isUrl(string $path): bool
    {
        return '' !== $path && filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
    }

    /**
     * Check if the given path has a windows drive-prefix, ie. "c:".
     *
     * @param string $path
     *
     * @return bool
     */
    public static function hasWindowsDrivePrefix(string $path)
    {
        return 1 < strlen($path) && ':' === $path[1] && ctype_alpha($path[0]);
    }

    /**
     * Calls clearstatcache to clear the realpath-cache for the given path.
     *
     * @param string $path
     */
    public static function cleanup(string $path)
    {
        clearstatcache(true, $path);
    }
}
