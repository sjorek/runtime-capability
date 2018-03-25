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
     * @param DetectorInterface $detector
     *
     * @return DetectorInterface
     *
     * @see AbstractManager::register()
     */
    public function register(DetectorInterface $detector): DetectorInterface
    {
        return parent::register($detector);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $idOrDetectorClass
     *
     * @return DetectorInterface
     *
     * @see AbstractManager::get()
     */
    public function get(string $idOrDetectorClass): DetectorInterface
    {
        return parent::get($idOrDetectorClass);
    }
}
