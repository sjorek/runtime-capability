<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalization;

use Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalizationDetectorInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\HierarchicalFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\PHP\HierarchicalFilesystemDriver;

/**
 * Class to detect unicode filesystem capabilities.
 *
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
    public function setup(array &$configuration)
    {
        parent::setup($configuration);
        $this->detectionFolderName = $this->getConfiguration('detection-folder-name', 'string');
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDetector::testFilesystemRead()
     */
    protected function testFilesystemRead(array &$normalizations, array $tests, array $fileNames)
    {
        $oldPath = $this->path;
        $this->createFolder($this->detectionFolderName);
        $this->filesystemDriver->setPath($this->detectionFolderName);
        parent::testFilesystemRead($normalizations, $tests, $fileNames);
        $this->filesystemDriver->setPath($oldPath);
        $this->filesystemDriver->remove($this->detectionFolderName);
    }

    /**
     * @param HierarchicalFilesystemDriverInterface $driver
     *
     * @return PathNormalizationDetectorInterface
     */
    protected function setFilesystemDriver(HierarchicalFilesystemDriverInterface $driver): PathNormalizationDetectorInterface
    {
        $this->filesystemDriver = $driver;

        return $this;
    }
}
