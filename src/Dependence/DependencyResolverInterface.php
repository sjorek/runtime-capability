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

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DependencyResolverInterface
{
    /**
     * Return a generator yielding DependableInterface::identify() => DependableInterface.
     *
     * @param DependencyInterface $dependency
     *
     * @return \Generator
     */
    public function resolve(DependencyInterface $dependency): \Generator;
}
