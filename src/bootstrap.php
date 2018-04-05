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

namespace Sjorek\RuntimeCapability\Utility {
    if (!function_exists('posix_geteuid')) {
        /**
         * @return int
         */
        function posix_geteuid()
        {
            return PosixUtility::ROOT_UID;
        }
    }
    if (!function_exists('posix_getegid')) {
        /**
         * @return int
         */
        function posix_getegid()
        {
            return PosixUtility::ROOT_GID;
        }
    }
    if (!function_exists('posix_getgroups')) {
        /**
         * @return int[]
         */
        function posix_getgroups()
        {
            return PosixUtility::USER_GROUPS;
        }
    }
}
