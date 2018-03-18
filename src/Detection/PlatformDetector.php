<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Detection;

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
            'engine' => 'php',
            'binary' => PHP_BINARY,
            'os' => PHP_OS,
            'os-family' => PHP_OS_FAMILY,
            'version' => PHP_VERSION,
            'version-id' => PHP_VERSION_ID,
        ];
    }
}
