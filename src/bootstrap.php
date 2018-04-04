<?php

namespace Sjorek\RuntimeCapability\Utility {
    if (!function_exists('posix_geteuid')) {
        /**
         * @return int
         */
        function posix_geteuid() {
            return PosixUtility::ROOT_UID;
        }
    }
    if (!function_exists('posix_getegid')) {
        /**
         * @return int
         */
        function posix_getegid() {
            return PosixUtility::ROOT_GID;
        }
    }
    if (!function_exists('posix_getgroups')) {
        /**
         * @return int[]
         */
        function posix_getgroups() {
            return PosixUtility::USER_GROUPS;
        }
    }
}