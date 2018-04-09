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

use Sjorek\RuntimeCapability\Dependence\DependencyManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DetectorManagerInterface extends DependencyManagerInterface
{
    /**
     * @param DetectorInterface $instance
     *
     * @return DetectorInterface
     */
    public function registerDetector(DetectorInterface $instance): DetectorInterface;

    /**
     * @param string $idOrDetectorClass
     *
     * @return DetectorInterface
     */
    public function createDetector(string $idOrDetectorClass): DetectorInterface;

    /**
     * Resolve the given detector's dependencies.
     *
     * @param DetectorInterface $instance
     *
     * @return \Generator yielding DetectorInterface::identify() => DetectorInterface
     */
    public function resolveDetectorDependencies(DetectorInterface $instance): \Generator;
}
