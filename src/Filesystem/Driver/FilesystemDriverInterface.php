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

namespace Sjorek\RuntimeCapability\Filesystem\Driver;

use Sjorek\RuntimeCapability\Filesystem\Strategy\FilesystemStrategyInterface;
use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemDriverInterface extends ManageableInterface, FilesystemStrategyInterface
{
    /**
     * @param FilesystemDriverManagerInterface $manager
     *
     * @return self
     */
    public function setFilesystemDriverManager(FilesystemDriverManagerInterface $manager): self;

    /**
     * @return FilesystemDriverManagerInterface
     */
    public function getFilesystemDriverManager(): FilesystemDriverManagerInterface;
}
