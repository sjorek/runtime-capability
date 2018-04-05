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
     * @var int
     */
    const ROOT_UID = 0;

    /**
     * @var int
     */
    const ROOT_GID = 0;

    /**
     * @var array
     */
    const USER_GROUPS = [];

    /**
     * returns current process owner's effective user id.
     *
     * If the system does not support posix_geteuid() the user will be root (0).
     *
     * @return int
     */
    public static function getEffectiveUser(): int
    {
        return posix_geteuid();
    }

    /**
     * returns current process owner's effective group id.
     *
     * If the system does not support posix_getegid() the group will be root (0).
     *
     * @return int
     */
    public static function getEffectiveGroup(): int
    {
        return posix_getegid();
    }

    /**
     * returns a list of current process owner's effective group id plus it's other group ids.
     *
     * If the system does not support posix_getgroups() the list of groups will contain root (0) only.
     *
     * @return array
     */
    public static function getUserGroups(): array
    {
        return array_merge([posix_getegid()], posix_getgroups());
    }
}
