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
interface TemporaryDirectoryStrategyInterface extends ExistingDirectoryStrategyInterface
{
    /**
     * We should use a sub-folder, as the operating might alter the given filenames.
     * A sub-folder is the only guaranteed chance to cleanup after detection.
     *
     * @var string
     */
    const TEMPORARY_FOLDER_NAME = '.runtime-capability-detection';

    /**
     * Creates the temporary directory.
     *
     * @return bool true on success
     */
    public function createDirectory();

    /**
     * Removes the temporary directory.
     *
     * @return bool true on success
     */
    public function removeDirectory();
}
