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

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Dependence\DependableInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DetectorInterface extends ConfigurableInterface, DependableInterface
{
    /**
     * @return array[bool[]]|bool[]|bool
     */
    public function detect();

    /**
     * @param DetectorManagerInterface $manager
     *
     * @return self
     */
    public function setDetectorManager(DetectorManagerInterface $manager): self;

    /**
     * @return DetectorManagerInterface
     */
    public function getDetectorManager(): DetectorManagerInterface;
}
