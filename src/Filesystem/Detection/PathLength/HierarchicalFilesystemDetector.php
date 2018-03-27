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

use Sjorek\RuntimeCapability\Filesystem\Driver\HierarchicalFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\HierarchicalFilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class HierarchicalFilesystemDetector extends FlatFilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => HierarchicalFilesystemDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
        'detection-folder-name' => self::DETECTION_FOLDER_NAME,
    ];

    /**
     * @var HierarchicalFilesystemDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $detectionFolderName;

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDetector::setup()
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
     * @see FlatFilesystemDetector::evaluate()
     */
    protected function evaluate()
    {
        $backupFilesystemPath = $this->filesystemPath;
        $realFilesystemPath = $this->filesystemDriver->setPath($this->filesystemPath);

        $this->filesystemDriver->createFolder($this->detectionFolderName);
        $this->filesystemPath = $this->detectionFolderName;

        $result = parent::evaluate();

        $this->filesystemPath = $backupFilesystemPath;
        $this->filesystemDriver->setPath($realFilesystemPath);
        $this->filesystemDriver->remove($this->detectionFolderName);

        return $result;
    }

    /**
     * @return HierarchicalFilesystemDriverInterface
     */
    protected function setupFilesystemDriver(): HierarchicalFilesystemDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . HierarchicalFilesystemDriverInterface::class)
        );
    }
}