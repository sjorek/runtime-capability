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

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractDependingDetector extends AbstractDetector implements DependingDetectorInterface
{
    /**
     * @var string[]
     */
    const DEPENDENCIES = [];

    /**
     * @var array[bool[]]|bool[]
     */
    protected $dependencies;

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::depends()
     */
    public function depends()
    {
        return static::DEPENDENCIES;
    }

    /**
     * {@inheritdoc}
     *
     * @see DependingDetectorInterface::setDependencies()
     */
    public function setDependencies(...$dependencies): DependingDetectorInterface
    {
        $this->dependencies = $dependencies;
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::detect()
     */
    public function detect()
    {
        $capabilities = $this->evaluate(...$this->dependencies);
        if ($this->compactResult) {
            $capabilities = $this->reduceResult($capabilities);
        }

        return $capabilities;
    }

    /**
     * @param array[bool[]]|bool[] ...$dependencies
     *
     * @return array[bool[]]|bool[]|bool
     *
     * @see AbstractDependingDetector::detect()
     */
    abstract protected function evaluate(...$dependencies);
}
