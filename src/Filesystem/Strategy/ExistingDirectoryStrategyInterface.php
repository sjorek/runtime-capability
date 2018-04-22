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

namespace Sjorek\RuntimeCapability\Filesystem\Strategy;

/**
 * Interface for filesystem specific functionality.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ExistingDirectoryStrategyInterface extends CurrentDirectoryStrategyInterface
{
    /**
     * Set path to operate on.
     *
     * @param mixed $path
     *
     * @return bool|mixed The path to operate on or false on failure
     */
    public function setDirectory($path);
}
