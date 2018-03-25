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
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemPathLengthDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriver::class
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Capability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return $this->filesystemDriver->getMaximumPathLength();
    }
}
