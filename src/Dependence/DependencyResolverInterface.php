<?php
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
     * @return \Generator
     */
    public function resolve(DependencyInterface $dependency) : \Generator;
}

