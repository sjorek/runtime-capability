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

namespace Sjorek\RuntimeCapability\Filesystem\Detection;

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Strategy\FilesystemStrategyInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait FilesystemDetectorTrait
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . FilesystemStrategyInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriverInterface::class,
    ];

    /**
     * @var FilesystemDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @return ConfigurableInterface
     *
     * @see ConfigurableInterface::setup()
     */
    public function setup(): ConfigurableInterface
    {
        parent::setup();

        $this->filesystemDriver = $this->setupFilesystemDriver(...static::FILESYSTEM_DRIVER_CONFIG_TYPES);

        return $this;
    }

    /**
     * @return FilesystemDriverInterface
     */
    protected function setupFilesystemDriver(string ...$types): FilesystemDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', ...$types)
        );
    }
}
