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

namespace Sjorek\RuntimeCapability\Filesystem\Detection;

use Sjorek\RuntimeCapability\Detection\DetectorInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemCaseSensitivityDetectorInterface extends DetectorInterface
{
    /**
     * We use a pattern to identify the test files that have been created.
     *
     * @var string
     */
    const DETECTION_FILENAME_PATTERN = '.case-sensitivity-test-%s.txt';

    /**
     * We should use a sub-folder, as the operating might alter the given filenames.
     * A sub-folder is the only guaranteed chance to cleanup after detection.
     *
     * @var string
     */
    const DETECTION_FOLDER_NAME = '.case-sensitivity-detection';
}
