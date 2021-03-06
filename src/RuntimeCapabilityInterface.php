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

namespace Sjorek\RuntimeCapability;

use Sjorek\RuntimeCapability\Capability\CapabilityManager;
use Sjorek\RuntimeCapability\Detection\DetectorManager;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManager;
use Sjorek\RuntimeCapability\Management\ManagementInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface RuntimeCapabilityInterface extends ManagementInterface
{
    /**
     * @return CapabilityManager
     */
    public function getCapabilityManager(): CapabilityManager;

    /**
     * @return DetectorManager
     */
    public function getDetectorManager(): DetectorManager;

    /**
     * @return FilesystemDriverManager
     */
    public function getFilesystemDriverManager(): FilesystemDriverManager;
}
