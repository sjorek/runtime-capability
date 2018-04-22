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
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\DirectoryTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface PathHierarchyDetectorInterface extends DetectorInterface
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . DirectoryTargetInterface::class,
    ];

    /**
     * We use a pattern to identify the test files that have been created.
     *
     * @var string
     */
    const DETECTION_DIRECTORYNAME_PATTERN = '.hierarchy-test-%s';
}
