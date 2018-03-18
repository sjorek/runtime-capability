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

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\FilesystemCaseSensitivityDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemCaseSensitivityDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriver::class,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

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
     * @see \Sjorek\RuntimeCapability\Capability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return $this->testFilesystem(sprintf($this->filenameDetectionPattern, 'aAbB'));
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    protected function testFilesystem(string $fileName): bool
    {
        return
            $this->filesystemDriver->create($fileName) &&
            $this->filesystemDriver->exists($fileName) &&
            $this->filesystemDriver->exists(strtolower($fileName)) &&
            $this->filesystemDriver->exists(strtoupper($fileName)) &&
            $this->filesystemDriver->remove()
        ;
    }
}
