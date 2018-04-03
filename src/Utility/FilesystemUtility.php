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
    public static function isAccessibleDirectory(string $path): bool
    {
        return self::isDirectory($path) && PosixUtility::isExecutablePath($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isWritableDirectory(string $path): bool
    {
        return self::isDirectory($path) && PosixUtility::isWritablePath($path);
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
    const LOCAL_URL_VALIDATION = (FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_PATH_REQUIRED);

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
            '' !== $path && (
                (
                    false === strpos($path, '://') &&
                    false === self::isUrl($path)
                ) || (
                    $path === filter_var($path, FILTER_VALIDATE_URL, self::LOCAL_URL_VALIDATION) &&
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
        return 1 < strlen($path) && ':' === $path[1] && self::isWindowsDriveLetter($path[0]);
    }

    /**
     * Calls clearstatcache to clear the realpath-cache for the given path.
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
    protected static function useWindowsPaths(): bool
    {
        return '\\' === DIRECTORY_SEPARATOR || '\\' === constant('DIRECTORY_SEPARATOR');
    }

    /**
     * @param string $letter
     * @return bool
     */
    protected static function isWindowsDriveLetter(string $letter): bool
    {
        // ctype_alpha() is locale LC_CTYPE dependent, therefore we do not use it here!
        // return ctype_alpha($letter[0]);
        return 1 === preg_match('/^[a-zA-Z]$/u', $letter);
    }
}
