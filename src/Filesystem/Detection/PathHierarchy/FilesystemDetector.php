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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\PathHierarchy;

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\PathHierarchyDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Target\DirectoryTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements PathHierarchyDetectorInterface
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . DirectoryTargetInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => DirectoryTargetDriver::class,
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
    ];

    /**
     * @var DirectoryTargetInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $detectionTargetPattern;

    /**
     * {@inheritdoc}
     *
     * @see AbstractFilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->detectionTargetPattern =
            $this->config('detection-target-pattern', 'match:^[A-Za-z0-9_.-]{1,100}%s[A-Za-z0-9_.-]{0,20}$')
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return $this->testFilesystem(sprintf($this->detectionTargetPattern, (string) time()));
    }

    /**
     * @param string $directoryName
     *
     * @return bool
     */
    protected function testFilesystem(string $directoryName): bool
    {
        $result = false;
        if ($this->filesystemDriver->createTarget($directoryName)) {
            $result = $this->filesystemDriver->existsTarget($directoryName);
            $this->filesystemDriver->removeTarget($directoryName);
        }

        return $result;
    }
}
