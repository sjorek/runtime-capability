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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemLinkTargetDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDirectoryDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDirectoryDetector extends FilesystemDetector
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => LinkTargetDirectoryDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var FilesystemLinkTargetDirectoryDriverInterface
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
        $this->filesystemPath = $this->filesystemDriver->setWorkingDirectory($this->filesystemPath);

        return parent::evaluate();
    }

    /**
     * @return FilesystemLinkTargetDirectoryDriverInterface
     */
    protected function setupFilesystemDriver(): FilesystemLinkTargetDirectoryDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FilesystemLinkTargetDirectoryDriverInterface::class)
        );
    }
}