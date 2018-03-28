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

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface HierarchicalFilesystemDriverInterface extends PathFilesystemDriverInterface
{
    /**
     * Creates a directory recursively.
     *
     * @param string      $path   A filename, -path or url
     *
     * @return mixed The path created
     */
    public function createFolder($path);

    /**
     * Removes a directory recursively.
     *
     * @param string      $path   A filename, -path or url
     *
     * @return mixed The path created
     */
    public function removeFolder($path);

    /**
     * Returns whether the path points to a folder.
     *
     * @param string      $path   A filename, -path or url
     *
     * @return bool
     */
    public function isFolder($path);
}
