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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\CaseSensitivity;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetInCurrentDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\CurrentDirectoryStrategyInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\LinkTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class CurrentDirectoryDetector extends FilesystemDetector
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . LinkTargetInterface::class,
        'subclass:' . CurrentDirectoryStrategyInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => LinkTargetInCurrentDirectoryDriver::class,
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
    ];
}
