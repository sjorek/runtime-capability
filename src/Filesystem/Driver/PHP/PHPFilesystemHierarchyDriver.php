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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
class PHPFilesystemHierarchyDriver extends PHPFilesystemDirectoryDriver implements FilesystemHierarchyDriverInterface
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
        parent::__construct($targetDriver);
        $this->directoryDriver = $directoryDriver;
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemHierarchyDriverInterface::createDirectory()
     */
    public function createDirectory($path)
    {
        return $this->directoryDriver->createTarget($this->prependWorkingDirectory($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemHierarchyDriverInterface::removeDirectory()
     */
    public function removeDirectory($path)
    {
        return $this->directoryDriver->removeTarget($this->prependWorkingDirectory($path));
    }
}
