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
     * Create an filesystem entry.
     *
     * @param string $path path of target to create or just its name
     *
     * @return bool true on success
     */
    public function createTarget($path): bool;

    /**
     * Returns whether the target exists.
     *
     * @param string $path path of target to check or just its name
     *
     * @return bool
     */
    public function targetExists($path): bool;

    /**
     * Removes given target.
     *
     * @param string $path path of target to remove or just its name
     *
     * @return bool true on success
     */
    public function removeTarget($path): bool;

    /**
     * Get the maximum path length.
     *
     * @return int
     */
    public function getMaximumPathLength(): int;
}
