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
interface FilesystemHierarchyDriverInterface extends FilesystemDirectoryDriverInterface
{
    /**
     * Creates a directory recursively.
     *
     * @param string $path relative directory path (or just it's name)
     *
     * @return mixed The path created
     */
    public function createDirectory($path);

    /**
     * Removes a directory recursively.
     *
     * @param string $path relative directory path (or just it's name)
     *
     * @return bool true on success
     */
    public function removeDirectory($path);
}
