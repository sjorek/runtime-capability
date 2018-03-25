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
class HierachicalFilesystemDetector extends FlatFilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => HierarchicalFilesystemDriver::class,
    ];

    /**
     * @var HierarchicalFilesystemDriverInterface
     */
    protected $filesystemDriver;

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
