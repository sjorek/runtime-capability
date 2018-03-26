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

use Sjorek\RuntimeCapability\Capability\CapabilityManagerInterface;
use Sjorek\RuntimeCapability\Detection\DetectorManagerInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManagerInterface;
use Sjorek\RuntimeCapability\Management\ManagementInterface;


/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface RuntimeCapabilityInterface extends ManagementInterface
{
    /**
     * @return CapabilityManagerInterface
     */
    public function getCapabilityManager(): CapabilityManagerInterface;

    /**
     * @return DetectorManagerInterface
     */
    public function getDetectorManager(): DetectorManagerInterface;

    /**
     * @return FilesystemDriverManagerInterface
     */
    public function getFilesystemDriverManager(): FilesystemDriverManagerInterface;
}
