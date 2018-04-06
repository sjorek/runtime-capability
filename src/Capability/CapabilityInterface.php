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

use Sjorek\RuntimeCapability\Dependence\DependencyManagerInterface;
use Sjorek\RuntimeCapability\Detection\DetectorInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface CapabilityInterface extends DependencyManagerInterface
{
    const MAXIMIMUM_EVALUATION_RETRIES = 2;

    /**
     * @return array[bool[]]|bool[]|bool
     */
    public function get();

    /**
     * @param CapabilityManagerInterface $manager
     *
     * @return CapabilityInterface
     */
    public function setManager(CapabilityManagerInterface $manager): self;

    /**
     * Return a generator yielding DetectorInterface::identify() => DetectorInterface.
     *
     * @param DetectorInterface $detector
     *
     * @return \Generator
     *
     * @see DependencyManagerInterface::resolve()
     */
    public function resolve(DetectorInterface $detector): \Generator;
}
