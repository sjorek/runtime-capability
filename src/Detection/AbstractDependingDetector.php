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
     * @see DetectorInterface::detect()
     */
    public function detect(...$dependencies)
    {
        $capabilities = $this->evaluate(...$dependencies);
        if ($this->compactResult) {
            $capabilities = $this->reduceResult($capabilities);
        }

        return $capabilities;
    }

    /**
     * @param array[bool[]]|bool[]|bool ...$dependencies
     *
     * @return array[bool[]]|bool[]|bool
     *
     * @see AbstractDetector::detect()
     */
    abstract protected function evaluate(...$dependencies);
}
