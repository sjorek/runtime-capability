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

namespace Sjorek\RuntimeCapability\Capability;

use Sjorek\RuntimeCapability\Filesystem\Detection\CaseSensitivity\FilesystemDetector as CaseSensitivityDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\Encoding\Utf8\FilesystemDetector as Utf8EncodingDetector;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemCapability extends AbstractCapability
{
    /**
     * {@inheritdoc}
     *
     * @see AbstractCapability::get()
     */
    public function get()
    {
        $manager = $this->manager->getManagement()->getDetectorManager();
        return $this->evaluate(
            $manager->get(CaseSensitivityDetector::class),
            $manager->get(Utf8EncodingDetector::class)
        );
    }
}
