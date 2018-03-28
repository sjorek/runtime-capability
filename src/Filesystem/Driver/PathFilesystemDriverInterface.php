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
interface PathFilesystemDriverInterface extends FilesystemDriverInterface
{
    /**
     * Get path to operate on.
     *
     * @return string|mixed
     */
    public function getPath();

    /**
     * Set path to operate on.
     *
     * @param null|mixed $path
     *
     * @return string|bool|mixed The path to operate on or false on failure
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
