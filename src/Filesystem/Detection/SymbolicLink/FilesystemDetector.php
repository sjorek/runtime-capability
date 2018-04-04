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

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\FilesystemSymbolicLinkDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemSymbolicLinkDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => LinkTargetDriver::class,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var LinkTargetDriverInterface
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
        return $this->testFilesystem(sprintf($this->filenameDetectionPattern, (string) time()));
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    protected function testFilesystem(string $fileName): bool
    {
        $result = false;
        if ($this->filesystemDriver->createTarget($fileName)) {
            $result = $this->filesystemDriver->existsTarget($fileName);
            $this->filesystemDriver->removeTarget($fileName);
        }

        return $result;
    }

    /**
     * @return LinkTargetDriverInterface
     */
    protected function setupFilesystemDriver(): LinkTargetDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . LinkTargetDriverInterface::class)
        );
    }
}
