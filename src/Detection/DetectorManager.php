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

use Sjorek\RuntimeCapability\Dependence\AbstractDependencyManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class DetectorManager extends AbstractDependencyManager implements DetectorManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see DetectorManagerInterface::registerDetector()
     */
    public function registerDetector(DetectorInterface $instance): DetectorInterface
    {
        return $this->registerDependency($instance);
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorManagerInterface::createDetector()
     */
    public function createDetector(string $idOrDetectorClass): DetectorInterface
    {
        return $this->createDependency($idOrDetectorClass);
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorManagerInterface::resolveDetectorDependencies()
     */
    public function resolveDetectorDependencies(DetectorInterface $instance): \Generator
    {
        return $this->resolveDependencies($instance);
    }
}
