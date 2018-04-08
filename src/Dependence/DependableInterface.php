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

namespace Sjorek\RuntimeCapability\Dependence;

use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DependableInterface extends ManageableInterface
{
    /**
     * @param DependencyManagerInterface $manager
     *
     * @return ManageableInterface
     */
    public function setDependencyManager(DependencyManagerInterface $manager): self;

    /**
     * @return DependencyManagerInterface
     */
    public function getDependencyManager(): DependencyManagerInterface;
}
