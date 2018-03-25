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

use Sjorek\RuntimeCapability\Capability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Dependence\DependableInterface;
use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DetectorInterface extends ManageableInterface, ConfigurableInterface, DependableInterface
{
    /**
     * @return array[bool[]]|bool[]|bool
     */
    public function detect();

    /**
     * @param DetectorManagerInterface $manager
     *
     * @return DetectorInterface
     */
    public function setManager(DetectorManagerInterface $manager): self;
}
