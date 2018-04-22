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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\PathHierarchy;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\Target\DirectoryTargetDirectoryDriverInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDirectoryDetector extends FilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => DirectoryTargetDirectoryDriver::class,
        'filesystem-path' => '.',
        'directoryname-detection-pattern' => self::DETECTION_DIRECTORYNAME_PATTERN,
    ];

    /**
     * @var DirectoryTargetDirectoryDriverInterface
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
        $this->filesystemPath = $this->filesystemDriver->setDirectory($this->filesystemPath);

        return parent::evaluate();
    }

    /**
     * @return DirectoryTargetDirectoryDriverInterface
     */
    protected function setupFilesystemDriver(): DirectoryTargetDirectoryDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . DirectoryTargetDirectoryDriverInterface::class)
        );
    }
}
