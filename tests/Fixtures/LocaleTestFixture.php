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
 * @param int         $category
 * @param int|string  $locale
 * @param string[]    ...$locales
 * @return string|false
 */
function setlocale(int $category, $locale, ...$locales) {

    if (isset($GLOBALS[__NAMESPACE__]['setlocale'][$category])) {
        return $GLOBALS[__NAMESPACE__]['setlocale'][$category];
    }

    if (0 === $locale) {
        return \setlocale($category, $locale, ...$locales);
    }

    \setlocale($category, $locale, ...$locales);

    return $GLOBALS[__NAMESPACE__]['setlocale'][$category] = $locale;
}

/**
 * @param int $item
 * @return string
 */
function nl_langinfo(int $item) {
    return $GLOBALS[__NAMESPACE__]['nl_langinfo'][$item] ?? \nl_langinfo($item);
}
