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

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Driver;

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FlatFilesystemDriverInterface extends FilesystemDriverInterface
{
    /**
     * Get path to operate on.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set path to operate on.
     *
     * @param null|mixed $path
     *
     * @return mixed The path to operate on
     */
    public function setPath($path = null);

    /**
     * @param string $pattern
     */
    public function setIteratorPattern($pattern);

    /**
     * Return a filename-yielding traversable object operating on the path entered, for use in foreach-loops.
     *
     * @return \Traversable
     */
    public function getIterator();
}
