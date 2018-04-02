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

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\FilesystemPathLengthDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemFileTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemPathLengthDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FileTargetDriver::class,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var FilesystemFileTargetDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $filenameDetectionPattern;

    /**
     * {@inheritdoc}
     *
     * @see AbstractFilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->filenameDetectionPattern =
            $this->config('filename-detection-pattern', 'match:^[A-Za-z0-9_.-]{1,100}%s[A-Za-z0-9_.-]{0,20}$')
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
        $maximumPathLength = $pathLength = $this->filesystemDriver->getMaximumPathLength();
        $result = false;
        while ($maximumPathLength > 0) {
            $pathLength = $maximumPathLength;
            --$maximumPathLength;
            $fileName = $this->generateDetectionFileName($pathLength);
            if (false === $fileName) {
                return false;
            }
            if ($this->testFilesystem($fileName)) {
                return $pathLength;
            }
        }

        return $result;
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    protected function testFilesystem($fileName)
    {
        return
            $this->filesystemDriver->createTarget($fileName) &&
            $this->filesystemDriver->existsTarget($fileName) &&
            $this->filesystemDriver->removeTarget($fileName)
        ;
    }

    /**
     * @param int $pathLength
     *
     * @return bool|string
     */
    protected function generateDetectionFileName($pathLength)
    {
        $pattern = $this->filenameDetectionPattern;
        $length =
            // $pattern - 2 x '%s'
            strlen($pattern) - 4 +
            strlen((string) $pathLength)
        ;
        if ($pathLength < ($length + 1)) {
            return false;
        }

        return sprintf($pattern, $pathLength, str_pad('', $pathLength - $length, 'x'));
    }

    /**
     * @return FilesystemFileTargetDriverInterface
     */
    protected function setupFilesystemDriver(): FilesystemFileTargetDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FilesystemFileTargetDriverInterface::class)
        );
    }
}
