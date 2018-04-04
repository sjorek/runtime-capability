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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\SymbolicLink;

use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetHierarchyDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemHierarchyDetector extends FilesystemDirectoryDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => LinkTargetHierarchyDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
        'detection-folder-name' => self::DETECTION_FOLDER_NAME,
    ];

    /**
     * @var LinkTargetHierarchyDriverInterface
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

    /**
     * @return LinkTargetHierarchyDriverInterface
     */
    protected function setupFilesystemDriver(): LinkTargetHierarchyDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . LinkTargetHierarchyDriverInterface::class)
        );
    }
}
