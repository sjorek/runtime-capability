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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\Hierarchy;

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\FilesystemHierarchyDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\DirectoryTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemHierarchyDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => DirectoryTargetDriver::class,
        'directoryname-detection-pattern' => self::DETECTION_DIRECTORYNAME_PATTERN,
    ];

    /**
     * @var DirectoryTargetDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $directorynameDetectionPattern;

    /**
     * {@inheritdoc}
     *
     * @see AbstractFilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->directorynameDetectionPattern =
            $this->config('directoryname-detection-pattern', 'match:^[A-Za-z0-9_.-]{1,100}%s[A-Za-z0-9_.-]{0,20}$')
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
        return $this->testFilesystem(sprintf($this->directorynameDetectionPattern, (string) time()));
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

    /**
     * @return DirectoryTargetDriverInterface
     */
    protected function setupFilesystemDriver(): DirectoryTargetDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . DirectoryTargetDriverInterface::class)
        );
    }
}
