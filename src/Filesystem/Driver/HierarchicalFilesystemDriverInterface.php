<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Driver;

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface HierarchicalFilesystemDriverInterface extends FlatFilesystemDriverInterface
{
    /**
     * Creates a directory recursively.
     *
     * @param string      $path   A filename, -path or url
     * @param null|string $parent A optional path to prepend, while operating with the given path
     *
     * @return mixed The path created
     */
    public function createFolder($path);

    /**
     * Returns whether the path points to a folder.
     *
     * @param string      $path   A filename, -path or url
     * @param null|string $parent A optional path to prepend, while operating with the given path
     *
     * @return bool
     */
    public function isFolder($path);
}
