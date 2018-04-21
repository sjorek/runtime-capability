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

if (!function_exists('posix_geteuid')) {
    /**
     * polyfill implementation
     *
     * @return int
     */
    function posix_geteuid()
    {
        return \Sjorek\RuntimeCapability\Utility\PosixUtility::ROOT_UID;
    }
}
if (!function_exists('posix_getegid')) {
    /**
     * polyfill implementation
     *
     * @return int
     */
    function posix_getegid()
    {
        return \Sjorek\RuntimeCapability\Utility\PosixUtility::ROOT_GID;
    }
}
if (!function_exists('posix_getgroups')) {
    /**
     * polyfill implementation
     *
     * @return int[]
     */
    function posix_getgroups()
    {
        return \Sjorek\RuntimeCapability\Utility\PosixUtility::USER_GROUPS;
    }
}

if (!function_exists('nl_langinfo')) {
    if (!defined('CODESET')) {
        // "nl_langinfo" polyfill implementation
        define('CODESET', 14);
    }
    /**
     * polyfill implementation
     *
     * @param int $item
     * @return string
     */
    function nl_langinfo($item)
    {
        return \Sjorek\RuntimeCapability\Utility\CharsetUtility::languageInfo($item);
    }
}
