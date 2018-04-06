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

namespace Sjorek\RuntimeCapability\Detection;

use Sjorek\RuntimeCapability\Management\AbstractManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class DetectorManager extends AbstractManager implements DetectorManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see DetectorManagerInterface::registerDetector()
     */
    public function registerDetector(DetectorInterface $driver): DetectorInterface
    {
        return $this->registerManageable($driver);
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorManagerInterface::createDetector()
     */
    public function createDetector(string $idOrDetectorClass): DetectorInterface
    {
        return $this->createManageable($idOrDetectorClass);
    }
}
