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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\PathLength;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetInTemporaryDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\TemporaryDirectoryStrategyInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\FileTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class TemporaryDirectoryDetector extends ExistingDirectoryDetector
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . FileTargetInterface::class,
        'subclass:' . TemporaryDirectoryStrategyInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FileTargetInTemporaryDirectoryDriver::class,
        'filesystem-path' => '.',
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
        'detection-folder-name' => self::DETECTION_FOLDER_NAME,
    ];

    /**
     * @var string
     */
    protected $detectionFolderName;

    /**
     * {@inheritdoc}
     *
     * @see ExistingDirectoryDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->detectionFolderName = $this->getConfiguration('detection-folder-name', 'match:^[A-Za-z0-9._-]+$');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExistingDirectoryDetector::evaluate()
     */
    protected function evaluate()
    {
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $backupFilesystemPath = $this->filesystemPath;

        $this->filesystemDriver->createDirectory($this->detectionFolderName);
        $this->filesystemPath = $this->detectionFolderName;

        $result = parent::evaluate();

        $this->filesystemPath = $backupFilesystemPath;
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $this->filesystemDriver->removeDirectory($this->detectionFolderName);

        return $result;
    }
}
