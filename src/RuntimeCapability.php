<?php

namespace Sjorek\RuntimeCapability;

use Sjorek\RuntimeCapability\Management\AbstractManagement;
use Sjorek\RuntimeCapability\Capability\CapabilityManagerInterface;
use Sjorek\RuntimeCapability\Capability\Detection\DetectorManagerInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FilesystemDriverManagerInterface;
use Sjorek\RuntimeCapability\Capability\Detection\DetectorManager;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FilesystemDriverManager;
use Sjorek\RuntimeCapability\Capability\CapabilityManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class RuntimeCapability extends AbstractManagement implements RuntimeCapabilityInterface
{
    /**
     * @return CapabilityManagerInterface
     */
    public function getCapabilityManager() : CapabilityManagerInterface
    {
        return $this->get(CapabilityManager::class);
    }

    /**
     * @return DetectorManagerInterface
     */
    public function getDetectorManager() : DetectorManagerInterface
    {
        return $this->get(DetectorManager::class);
    }

    /**
     * @return FilesystemDriverManagerInterface
     */
    public function getFilesystemDriverManager() : FilesystemDriverManagerInterface
    {
        return $this->get(FilesystemDriverManager::class);
    }
}

