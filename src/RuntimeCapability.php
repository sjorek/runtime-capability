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
use Sjorek\RuntimeCapability\Capability\CapabilityManagerInterface;
use Sjorek\RuntimeCapability\Detection\DetectorManager;
use Sjorek\RuntimeCapability\Detection\DetectorManagerInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManager;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManagerInterface;
use Sjorek\RuntimeCapability\Management\AbstractManagement;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class RuntimeCapability extends AbstractManagement implements RuntimeCapabilityInterface
{
    /**
     * @return CapabilityManagerInterface
     */
    public function getCapabilityManager(): CapabilityManagerInterface
    {
        return $this->get(CapabilityManager::class);
    }

    /**
     * @return DetectorManagerInterface
     */
    public function getDetectorManager(): DetectorManagerInterface
    {
        return $this->get(DetectorManager::class);
    }

    /**
     * @return FilesystemDriverManagerInterface
     */
    public function getFilesystemDriverManager(): FilesystemDriverManagerInterface
    {
        return $this->get(FilesystemDriverManager::class);
    }
}
