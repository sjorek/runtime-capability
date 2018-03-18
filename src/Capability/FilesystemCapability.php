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

namespace Sjorek\RuntimeCapability\Capability;

use Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalization\FilesystemDetector;

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
        return $this->evaluate(
            $this->manager->getManagement()->getDetectorManager()->get(FilesystemDetector::class)
        );
    }
}
