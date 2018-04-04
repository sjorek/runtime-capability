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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\PathEncoding;

use Sjorek\RuntimeCapability\Filesystem\Driver\FileTargetHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetHierarchyDriver;

/**
 * Class to detect unicode filesystem capabilities.
 *
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
        'filepath-encoding' => 'BINARY',
        'filename-tests' => self::UTF8_FILENAME_TESTS,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
        'detection-folder-name' => self::DETECTION_FOLDER_NAME,
    ];

    /**
     * @var FileTargetHierarchyDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $detectionFolderName;

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDirectoryDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->detectionFolderName = $this->getConfiguration('detection-folder-name', 'match:^[A-Za-z0-9._-]+$');

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @see FilesystemDirectoryDetector::evaluate()
     */
    protected function evaluate(array $localeCharset, string $defaultCharset)
    {
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $backupFilesystemPath = $this->filesystemPath;

        $this->filesystemDriver->createDirectory($this->detectionFolderName);
        $this->filesystemPath = $this->detectionFolderName;

        $result = parent::evaluate($localeCharset, $defaultCharset);

        $this->filesystemPath = $backupFilesystemPath;
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $this->filesystemDriver->removeDirectory($this->detectionFolderName);

        return $result;
    }

    /**
     * @return FileTargetHierarchyDriverInterface
     */
    protected function setupFilesystemDriver(): FileTargetHierarchyDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FileTargetHierarchyDriverInterface::class)
        );
    }
}
