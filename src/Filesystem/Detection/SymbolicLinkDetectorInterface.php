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

namespace Sjorek\RuntimeCapability\Filesystem\Detection;

use Sjorek\RuntimeCapability\Detection\DetectorInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface SymbolicLinkDetectorInterface extends DetectorInterface
{
    /**
     * We use a pattern to identify the test links that have been created.
     *
     * @var string
     */
    const DETECTION_TARGET_PATTERN = '.symbolic-link-test-%s.txt';
}
