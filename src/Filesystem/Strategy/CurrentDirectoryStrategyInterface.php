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
interface CurrentDirectoryStrategyInterface extends FilesystemStrategyInterface, \IteratorAggregate
{
    /**
     * Get path to operate on.
     *
     * @return mixed
     */
    public function getDirectory();

    /**
     * @param string $pattern
     */
    public function setIteratorPattern($pattern);
}
