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
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetInExistingDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\ExistingDirectoryStrategyInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\FileTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class ExistingDirectoryDetector extends CurrentDirectoryDetector
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . FileTargetInterface::class,
        'subclass:' . ExistingDirectoryStrategyInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FileTargetInExistingDirectoryDriver::class,
        'filesystem-path' => '.',
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
    ];

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
        $this->filesystemDriver->setDirectory($this->filesystemPath);

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
            strlen($this->detectionTargetPattern) - 4 +
            strlen((string) $pathLength)
        ;
        if ($pathLength < ($length + 1)) {
            return false;
        }

        return parent::generateDetectionFileName($pathLength);
    }
}
