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
 * @param string $arg
 */
function escapeshellarg(string $arg)
{
    return $GLOBALS[__NAMESPACE__]['escapeshellarg'] ?? \escapeshellarg($arg);
}
