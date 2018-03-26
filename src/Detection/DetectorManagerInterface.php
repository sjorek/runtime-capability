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

use Sjorek\RuntimeCapability\Management\ManagerInterface;
use Sjorek\RuntimeCapability\RuntimeCapabilityInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DetectorManagerInterface extends ManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param DetectorInterface $detector
     *
     * @return DetectorInterface
     *
     * @see ManagerInterface::register()
     */
    public function register(DetectorInterface $detector): DetectorInterface;

    /**
     * {@inheritdoc}
     *
     * @param string $idOrDetectorClass
     *
     * @return DetectorInterface
     *
     * @see ManagerInterface::get()
     */
    public function get(string $idOrDetectorClass): DetectorInterface;

    /**
     * {@inheritdoc}
     *
     * @return RuntimeCapabilityInterface
     *
     * @see ManagerInterface::getManagement()
     */
    public function getManagement(): RuntimeCapabilityInterface;
}
