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
interface FilesystemHierarchyDetectorInterface extends DetectorInterface
{
    /**
     * We use a pattern to identify the test files that have been created.
     *
     * @var string
     */
    const DETECTION_DIRECTORYNAME_PATTERN = '.hierarchy-test-%s';
}
