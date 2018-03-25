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

use Sjorek\RuntimeCapability\Detection\AbstractDetector;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Detection\AbstractDependingDetector;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractFilesystemDetector extends AbstractDependingDetector
{
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
     * {@inheritdoc}
     *
     * @see AbstractDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $this->filesystemDriver = $this->setupFilesystemDriver();

        return $this;
    }

    /**
     * @return FilesystemDriverInterface
     */
    protected function setupFilesystemDriver(): FilesystemDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FilesystemDriverInterface::class)
        );
    }
}
