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
        $_identifiers = [];
        $_instances = [];

        return $this->resolveDependenciesWithCircularProtection($instance, $_identifiers, $_instances);
    }

    /**
     * @param DependableInterface $instance
     * @param array               $_identifiers
     * @param array               $_instances
     *
     * @throws \RuntimeException
     *
     * @return \Generator
     */
    protected function resolveDependenciesWithCircularProtection(
        DependableInterface $instance,
        array &$_identifiers,
        array &$_instances): \Generator
    {
        $id = $instance->identify();
        if (in_array($id, $_identifiers, true)) {
            throw new \RuntimeException(
                sprintf('Invalid circular dependency for id: %s', $id),
                1521250751
            );
        }
        if (in_array($instance, $_instances, true)) {
            throw new \RuntimeException(
                sprintf('Invalid circular dependency for instance: %s', get_class($instance)),
                1521250755
            );
        }
        if ($instance instanceof DependingInterface) {
            $generator = function (array &$_identifiers, array &$_instances) use ($id, $instance) {
                foreach ($instance->depends() as $id) {
                    yield from $this->resolveDependenciesWithCircularProtection(
                        $this->createManageable($id),
                        $_identifiers,
                        $_instances
                    );
                }
                yield $id => $instance;
            };
        } else {
            $generator = function () use ($id, $instance) {
                yield $id => $instance;
            };
        }

        return $generator($_identifiers, $_instances);
    }
}
