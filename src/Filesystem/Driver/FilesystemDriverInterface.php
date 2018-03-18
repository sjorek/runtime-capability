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

use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemDriverInterface extends ManageableInterface
{
    /**
     * Create an empty or touch an existing file.
     *
     * @param string $path A filename, -path or url
     *
     * @return bool false on failure
     */
    public function create($path);

    /**
     * Returns whether the path already exists.
     *
     * @param string $path A filename, -path or url
     *
     * @return bool
     */
    public function exists($path);

    /**
     * Removes given filename, -path or url.
     *
     * @param string $path A path to remove
     *
     * @return bool false on failure
     */
    public function remove($path);
}
