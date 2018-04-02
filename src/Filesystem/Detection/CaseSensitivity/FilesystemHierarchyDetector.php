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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\CaseSensitivity;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemFileTargetHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetHierarchyDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemHierarchyDetector extends FilesystemDirectoryDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FileTargetHierarchyDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
        'detection-folder-name' => self::DETECTION_FOLDER_NAME,
    ];

    /**
     * @var FilesystemFileTargetHierarchyDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $detectionFolderName;

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDetector::setup()
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
     * @see FilesystemDirectoryDetector::evaluate()
     */
    protected function evaluate()
    {
        $this->filesystemDriver->setWorkingDirectory($this->filesystemPath);
        $backupFilesystemPath = $this->filesystemPath;

        $this->filesystemDriver->createDirectory($this->detectionFolderName);
        $this->filesystemPath = $this->detectionFolderName;

        $result = parent::evaluate();

        $this->filesystemPath = $backupFilesystemPath;
        $this->filesystemDriver->setWorkingDirectory($this->filesystemPath);
        $this->filesystemDriver->removeDirectory($this->detectionFolderName);

        return $result;
    }

    /**
     * @return FilesystemFileTargetHierarchyDriverInterface
     */
    protected function setupFilesystemDriver(): FilesystemFileTargetHierarchyDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FilesystemFileTargetHierarchyDriverInterface::class)
        );
    }
}
