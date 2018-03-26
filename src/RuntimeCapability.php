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
use Sjorek\RuntimeCapability\Management\AbstractManagement;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class RuntimeCapability extends AbstractManagement implements RuntimeCapabilityInterface
{
    /**
     * @var CapabilityManager
     */
    protected $capabilityManager = null;

    /**
     * @var DetectorManager
     */
    protected $detectorManager = null;

    /**
     * @var FilesystemDriverManager
     */
    protected $filesystemDriverManager = null;

    /**
     * @return CapabilityManager
     */
    public function getCapabilityManager(): CapabilityManager
    {
        if (null !== $this->capabilityManager) {
            return $this->capabilityManager;
        }

        return $this->capabilityManager = $this->createManager(CapabilityManager::class);
    }

    /**
     * @return DetectorManager
     */
    public function getDetectorManager(): DetectorManager
    {
        if (null !== $this->detectorManager) {
            return $this->detectorManager;
        }

        return $this->detectorManager = $this->createManager(DetectorManager::class);
    }

    /**
     * @return FilesystemDriverManager
     */
    public function getFilesystemDriverManager(): FilesystemDriverManager
    {
        if (null !== $this->filesystemDriverManager) {
            return $this->filesystemDriverManager;
        }

        return $this->filesystemDriverManager = $this->createManager(FilesystemDriverManager::class);
    }
}
