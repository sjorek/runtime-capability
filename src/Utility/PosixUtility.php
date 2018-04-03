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
final class PosixUtility
{
    /**
     * @var integer
     */
    const OWNER_ROOT = 0;

    /**
     * @var integer
     */
    const GROUP_ROOT = 0;

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isExecutablePath(string $path): bool
    {
        return
            FilesystemUtility::pathExists($path) &&
            (
                // TODO Find out why is_executable() fails for some vfs-directories
                is_executable($path) ||
                (
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    ($permissions = (@fileperms($path) ?: 0)) &&
                    (
                        (
                            self::getEffectiveUser() === @fileowner($path) &&
                            ($permissions & 0x0040) && !($permissions & 0x0800) // owner executable flag - [u]ser
                        ) || (
                            in_array(@filegroup($path), self::getUserGroups(), true) &&
                            ($permissions & 0x0008) && !($permissions & 0x0400) // group executable flag - [g]roup
                        ) || (
                            ($permissions & 0x0001) && !($permissions & 0x0200) // world executable flag - [o]ther
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
    public static function isWritablePath(string $path): bool
    {
        return
            FilesystemUtility::pathExists($path) &&
            (
                // TODO Find out why is_writable() fails for some vfs-directories
                is_writable($path) ||
                // TODO Remove the fileperms() workaround for vfs-directories
                // @see http://php.net/manual/en/function.fileperms.php#example-2671
                ($permissions = (@fileperms($path) ?: 0)) &&
                (
                    (
                        self::getEffectiveUser() === @fileowner($path) &&
                        ($permissions & 0x0080)                                 // owner writable flag - [u]ser
                    ) || (
                        in_array(@filegroup($path), self::getUserGroups(), true) &&
                        ($permissions & 0x0010)                                 // group writable flag - [g]roup
                    ) || (
                        ($permissions & 0x0002)                                 // world writable flag - [o]ther
                    )
                )
            )
        ;
    }

    /**
     * returns current process owner's effective user id
     *
     * If the system does not support posix_geteuid() the user will be root (0).
     *
     * @return  int
     */
    protected static function getEffectiveUser(): int
    {
        return function_exists('posix_geteuid') ? posix_geteuid() : self::OWNER_ROOT;
    }

    /**
     * returns current process owner's effective group id
     *
     * If the system does not support posix_getegid() the group will be root (0).
     *
     * @return  int
     */
    protected static function getEffectiveGroup(): int
    {
        return function_exists('posix_getegid') ? posix_getegid() : self::GROUP_ROOT;
    }

    /**
     * returns a list of current process owner's effective group id plus it's other group ids
     *
     * If the system does not support posix_getgroups() the list of groups will contain root (0) only.
     *
     * @return  array
     */
    protected static function getUserGroups(): array
    {
        return array_merge(
            [self::getEffectiveGroup()],
            function_exists('posix_getgroups') ? posix_getgroups() : []
        );
    }

}
