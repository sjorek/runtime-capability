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

use Sjorek\RuntimeCapability\Filesystem\Driver\FlatFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FlatFilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FlatFilesystemDetector extends FilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FlatFilesystemDriver::class
    ];

    /**
     * @var FlatFilesystemDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @return FlatFilesystemDriverInterface
     */
    protected function setupFilesystemDriver(): FlatFilesystemDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FlatFilesystemDriverInterface::class)
        );
    }
}
