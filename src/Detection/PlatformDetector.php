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
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PlatformDetector extends AbstractDetector
{
    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return [
            'name' => defined('HHVM_VERSION') ? 'hhvm' : 'php',
            'binary' => defined('PHP_BINARY') ? PHP_BINARY : null,
            'os' => PHP_OS,
            'os-family' => PHP_OS_FAMILY,
            'version' => defined('HHVM_VERSION') ? HHVM_VERSION : PHP_VERSION,
            'version-id' => PHP_VERSION_ID,
        ];
    }
}
