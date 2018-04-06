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

use Sjorek\RuntimeCapability\Management\ManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DependencyManagerInterface extends ManagerInterface
{
    /**
     * Return a generator yielding DependableInterface::identify() => DependableInterface.
     *
     * @param DependableInterface $instance
     *
     * @return \Generator
     */
    public function resolveDependencies(DependableInterface $instance): \Generator;
}
