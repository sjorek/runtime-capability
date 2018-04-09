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

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Configuration\ConfigurableTrait;
use Sjorek\RuntimeCapability\Dependence\AbstractDependable;
use Sjorek\RuntimeCapability\Dependence\DependableInterface;
use Sjorek\RuntimeCapability\Dependence\DependencyManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractDetector extends AbstractDependable implements DetectorInterface
{
    use ConfigurableTrait;

    /**
     * @var array
     */
    protected static $DEFAULT_CONFIGURATION = [
        'compact-result' => false,
    ];

    /**
     * @var DetectorManagerInterface
     */
    protected $manager = null;

    /**
     * @var bool
     */
    protected $compactResult = null;

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::setDetectorManager()
     */
    public function setDetectorManager(DetectorManagerInterface $manager): DetectorInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::getDetectorManager()
     */
    public function getDetectorManager(): DetectorManagerInterface
    {
        if (null !== $this->manager) {
            return $this->manager;
        }

        throw new \RuntimeException('Missing manager instance.', 1522098121);
    }

    /**
     * {@inheritdoc}
     *
     * @see DependableInterface::setDependencyManager()
     */
    public function setDependencyManager(DependencyManagerInterface $manager): DependableInterface
    {
        return $this->setDetectorManager($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @see DependableInterface::getDependencyManager()
     */
    public function getDependencyManager(): DependencyManagerInterface
    {
        return $this->getDetectorManager();
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setup()
     */
    public function setup(): ConfigurableInterface
    {
        $this->compactResult = $this->config('compact-result', 'boolean');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::detect()
     */
    public function detect()
    {
        $capabilities = $this->evaluate();
        if ($this->compactResult && is_array($capabilities)) {
            $capabilities = $this->reduceResult($capabilities);
        }

        return $capabilities;
    }

    /**
     * @return array[bool[]]|bool[]|bool
     *
     * @see AbstractDetector::detect()
     */
    abstract protected function evaluate();

    /**
     * Reduce the first two levels of the given multidimensional array to the most compact value.
     *
     * @param bool|bool[] $capabilities
     */
    protected function reduceResult(array $capabilities)
    {
        $isNotBoolean = function ($value) {
            return !is_bool($value);
        };
        $reduce = function ($capability) use ($isNotBoolean) {
            if (false === $capability || true === $capability) {
                return $capability;
            }
            if (is_array($capability) && empty(array_filter($capability, $isNotBoolean))) {
                if (!in_array(false, $capability, true)) {
                    return true;
                }
                if (!in_array(true, $capability, true)) {
                    return false;
                }
            }

            return $capability;
        };

        return $reduce(array_map($reduce, $capabilities));
    }
}
