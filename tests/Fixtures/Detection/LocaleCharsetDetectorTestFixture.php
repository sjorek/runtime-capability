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

namespace Sjorek\RuntimeCapability\Detection;

/**
 * @param int         $category
 * @param int|string  $locale
 * @param string[]    ...$locales
 * @return string|false
 */
function setlocale(int $category, $locale, ...$locales) {
    return \Sjorek\RuntimeCapability\Utility\setlocale($category, $locale, ...$locales);
}

/**
 * @param int $item
 * @return string
 */
function nl_langinfo(int $item) {
    return \Sjorek\RuntimeCapability\Utility\nl_langinfo($item);
}
