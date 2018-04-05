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
     * Check for symbolic link existence - which means only symlinks.
     * Uses is_link() to check for symlink existence.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isSymbolicLink(string $path): bool
    {
        return is_link($path);
    }

    /**
     * Check for directory existence - which means only directories.
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
     * @param string $path
     *
     * @return bool
     */
    public static function isExecutableDirectory(string $path): bool
    {
        return self::isDirectory($path) && self::isExecutablePath($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isExecutablePath(string $path): bool
    {
        return
            self::isLocalPath($path) && self::pathExists($path) &&
            (
                // TODO Find out why is_executable() fails for some vfs-directories
                is_executable($path) ||
                (
                    !self::useWindowsPaths() &&
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    0 !== ($perms = (@fileperms($path) ?: 0)) &&
                    (
                        (
                            ($perms & 0x0040) && !($perms & 0x0800) &&          // owner executable flag - [u]ser
                            posix_geteuid() === @fileowner($path)
                        ) || (
                            ($perms & 0x0008) && !($perms & 0x0400) &&          // group executable flag - [g]roup
                            in_array(@filegroup($path), PosixUtility::getUserGroups(), true)
                        ) || (
                            ($perms & 0x0001) && !($perms & 0x0200)             // world executable flag - [o]ther
                        )
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
        return self::isDirectory($path) && self::isWritablePath($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isWritablePath(string $path): bool
    {
        return
            self::isLocalPath($path) && self::pathExists($path) &&
            (
                // TODO Find out why is_writable() fails for some vfs-directories
                is_writable($path) ||
                (
                    !self::useWindowsPaths() &&
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    0 !== ($perms = (@fileperms($path) ?: 0)) &&
                    (
                        (
                            ($perms & 0x0080) &&                                  // owner writable flag - [u]ser
                            posix_geteuid() === @fileowner($path)
                        ) || (
                            ($perms & 0x0010) &&                                  // group writable flag - [g]roup
                            in_array(@filegroup($path), PosixUtility::getUserGroups(), true)
                        ) || (
                            ($perms & 0x0002)                                     // world writable flag - [o]ther
                        )
                    )
                )
            )
        ;
    }

    /**
     * Normalize the given path.
     *
     * Hint: EMPTY = ''
     *
     * <pre>
     * .            =>  EMPTY       # replace single dot with current working directory
     * ./test       =>  test        # replace leading dot with current working directory in path
     * .\test       =>  test        # replace leading dot with current working directory in windows path
     * test/file    =>  test/file   # prepend current working directory to relative path
     * test\file    =>  test/file   # prepend current working directory to relative windows path
     * </pre>
     *
     * @param string $path
     *
     * @return string
     */
    public static function normalizePath(string $path): string
    {
        if ('.' === $path) {
            $path = '';
        }

        if ('' === $path || self::isUrl($path)) {
            return rtrim($path, '/');
        }

        $path = rtrim(strtr($path, '\\', '/'), '/');
        if ('' === $path) {
            $path = '/';
        }

        if (self::isAbsolutePath($path)) {
            return $path;
        }

        if ('.' === $path) {
            $path = '';
        } elseif (1 < strlen($path) && '.' === $path[0] && '/' === $path[1]) {
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

        if (self::useWindowsPaths() && 2 === strlen($cwd) && self::hasWindowsDrivePrefix($cwd)) {
            $cwd .= '/';
        }

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

        if (self::useWindowsPaths()) {
            return in_array($path[self::hasWindowsDrivePrefix($path) ? 2 : 0] ?? null, ['/', '\\'], true);
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
        return
            false !== strpos($path, '://') ||
            $path === filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)
        ;
    }

    /**
     * @var int
     */
    const LOCAL_URL_VALIDATION_HOST = (FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED);

    /**
     * @var int
     */
    const LOCAL_URL_VALIDATION_PATH = (FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_PATH_REQUIRED);

    /**
     * @var string[]
     */
    const LOCAL_URL_SCHEMES = ['file', 'vfs'];

    /**
     * Check if the path represents a local filesystem path.
     * A local filesystem path includes urls starting with "file:" or "vfs:" .
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isLocalPath(string $path): bool
    {
        return
            !in_array($path, ['', 'file:', 'vfs:'], true) &&
            (
                (
                    false === strpos($path, '://') &&
                    false === self::isUrl($path)
                ) || (
                    (
                        $path === filter_var($path, FILTER_VALIDATE_URL, self::LOCAL_URL_VALIDATION_HOST) ||
                        $path === filter_var($path, FILTER_VALIDATE_URL, self::LOCAL_URL_VALIDATION_PATH)
                    ) &&
                    in_array(parse_url($path, PHP_URL_SCHEME), self::LOCAL_URL_SCHEMES, true)
                )
            )
        ;
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
        // ctype_alpha() is locale LC_CTYPE dependent, therefore we do not use it here!
        // return 1 < strlen($path) && ':' === $path[1] && ctype_alpha($letter[0]);
        return 1 === preg_match('/^[a-zA-Z]:/u', $path);
    }

    /**
     * Calls clearstatcache to clear the realpath-cache for the given local path.
     *
     * @codeCoverageIgnore
     *
     * @param string $path
     */
    public static function cleanup(string $path)
    {
        if (self::isLocalPath($path)) {
            clearstatcache(true, $path);
        }
    }

    /**
     * @return bool
     */
    public static function useWindowsPaths(): bool
    {
        return
            '\\' === DIRECTORY_SEPARATOR ||
            // this allows overloading
            '\\' === constant('DIRECTORY_SEPARATOR')
        ;
    }
}
