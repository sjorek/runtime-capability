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

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetInTemporaryDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\TemporaryDirectoryStrategyInterface;
use Sjorek\RuntimeCapability\Filesystem\Target\LinkTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class TemporaryDirectoryDetector extends ExistingDirectoryDetector
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . LinkTargetInterface::class,
        'subclass:' . TemporaryDirectoryStrategyInterface::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => LinkTargetInTemporaryDirectoryDriver::class,
        'filesystem-path' => '.',
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
        'temporary-folder-name' => TemporaryDirectoryStrategyInterface::TEMPORARY_FOLDER_NAME,
    ];

    /**
     * @var string
     */
    protected $temporaryFolderName;

    /**
     * {@inheritdoc}
     *
     * @see ExistingDirectoryDetector::setup()
     */
    public function setup(): ConfigurableInterface
    {
        parent::setup();
        $this->temporaryFolderName = $this->getConfiguration('temporary-folder-name', 'match:^[A-Za-z0-9._-]+$');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ExistingDirectoryDetector::evaluate()
     */
    protected function evaluate()
    {
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $backupFilesystemPath = $this->filesystemPath;

        $this->filesystemDriver->createDirectory($this->temporaryFolderName);
        $this->filesystemPath = $this->temporaryFolderName;

        $result = parent::evaluate();

        $this->filesystemPath = $backupFilesystemPath;
        $this->filesystemDriver->setDirectory($this->filesystemPath);
        $this->filesystemDriver->removeDirectory($this->temporaryFolderName);

        return $result;
    }
}
