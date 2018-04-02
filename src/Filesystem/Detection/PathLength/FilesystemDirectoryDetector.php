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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemFileTargetDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDirectoryDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDirectoryDetector extends FilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FileTargetDirectoryDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var FilesystemFileTargetDirectoryDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $filesystemPath;

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->filesystemPath = $this->config('filesystem-path', 'match:^\.?(?:[^.]|\.[^.])*$');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDetector::evaluate()
     */
    protected function evaluate()
    {
        $this->filesystemDriver->setWorkingDirectory($this->filesystemPath);

        return parent::evaluate();
    }

    /**
     * @param int $pathLength
     *
     * @return bool|string
     */
    protected function generateDetectionFileName($pathLength)
    {
        $length =
            strlen($this->filesystemPath) + strlen(DIRECTORY_SEPARATOR) +
            // $pattern - 2 x '%s'
            strlen($this->filenameDetectionPattern) - 4 +
            strlen((string) $pathLength)
        ;
        if ($pathLength < ($length + 1)) {
            return false;
        }

        return parent::generateDetectionFileName($pathLength);
    }

    /**
     * @return FilesystemFileTargetDirectoryDriverInterface
     */
    protected function setupFilesystemDriver(): FilesystemFileTargetDirectoryDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FilesystemFileTargetDirectoryDriverInterface::class)
        );
    }
}
