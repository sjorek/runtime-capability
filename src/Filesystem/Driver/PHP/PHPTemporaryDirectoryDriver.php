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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\TemporaryDirectoryStrategyInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
class PHPTemporaryDirectoryDriver extends PHPExistingDirectoryDriver implements TemporaryDirectoryStrategyInterface
{
    /**
     * @var DirectoryTargetDriver
     */
    protected $directoryDriver;

    /**
     * @param PHPFilesystemDriverInterface $targetDriver
     * @param DirectoryTargetDriver        $directoryDriver
     */
    public function __construct(PHPFilesystemDriverInterface $targetDriver = null,
                                DirectoryTargetDriver $directoryDriver = null)
    {
        if (null === $directoryDriver) {
            $directoryDriver = new DirectoryTargetDriver();
        }
        $this->directoryDriver = $directoryDriver;

        parent::__construct($targetDriver);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Strategy\TemporaryDirectoryStrategyInterface::createDirectory()
     */
    public function createDirectory($path)
    {
        return $this->directoryDriver->createTarget($this->prependDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Strategy\TemporaryDirectoryStrategyInterface::removeDirectory()
     */
    public function removeDirectory($path)
    {
        return $this->directoryDriver->removeTarget($this->prependDirectory($path));
    }
}
