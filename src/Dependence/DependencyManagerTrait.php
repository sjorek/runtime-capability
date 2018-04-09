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
trait DependencyManagerTrait
{
    /**
     * @param DependableInterface $instance
     *
     * @return DependableInterface
     */
    public function registerDependency(DependableInterface $instance): DependableInterface
    {
        return $this->registerManageable($instance);
    }

    /**
     * @param string $idOrDependableClass
     *
     * @throws \InvalidArgumentException
     *
     * @return DependableInterface
     */
    public function createDependency(string $idOrDependableClass): DependableInterface
    {
        return $this->createManageable($idOrDependableClass);
    }

    /**
     * Return a generator yielding DependableInterface::identify() => DependableInterface.
     *
     * @param DependableInterface $instance
     *
     * @return \Generator
     *
     * @see DependencyManagerInterface::resolveDependencies()
     */
    public function resolveDependencies(DependableInterface $instance): \Generator
    {
        return $this->generateDependencyChain($instance);
    }

    /**
     * @param DependableInterface $instance
     * @param null|array          $_dependencies
     * @param null|array          $_dependents
     * @param null|string         $_parent
     *
     * @throws \RuntimeException
     *
     * @return \Generator
     */
    protected function generateDependencyChain(
        DependableInterface $instance,
        array &$_dependencies = null,
        array &$_dependents = null,
        string $_parent = null)
    {
        if (null === $_dependencies) {
            $_dependencies = [];
        }
        if (null === $_dependents) {
            $_dependents = [];
        }
        if (null === $_parent) {
            $_parent = '_';
        }

        $id = $instance->identify();
        if (isset($_dependencies[$id])) {
            throw new \LogicException(
                sprintf(
                    'Invalid circular dependency for id "%s" (%s) with parent id "%s".',
                    $id,
                    get_class($instance),
                    $_parent
                ),
                1521250751
            );
        }
        $_dependencies[$_parent][] = $id;
        $_dependents[$id][] = $_parent;

        if ($instance instanceof DependingInterface) {
            foreach ($instance->depends() as $dependency) {
                yield from $this->generateDependencyChain(
                    $this->createDependency($dependency),
                    $_dependencies,
                    $_dependents,
                    $id
                );
                $_dependencies[$_parent][] = $dependency;
                $_dependents[$dependency][] = $_parent;
            }
        }

        yield $id => $instance;
    }
}
