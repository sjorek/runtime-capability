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
     * @param DependableInterface $instance
     *
     * @return DependableInterface
     */
    public function registerDependency(DependableInterface $instance): DependableInterface;

    /**
     * @param string $idOrDependableClass
     *
     * @throws \InvalidArgumentException
     *
     * @return DependableInterface
     */
    public function createDependency(string $idOrDependableClass): DependableInterface;

    /**
     * Resolve the given dependable's dependencies.
     *
     * @param DependableInterface $instance
     *
     * @return \Generator yielding DependableInterface::identify() => DependableInterface
     */
    public function resolveDependencies(DependableInterface $instance): \Generator;
}
