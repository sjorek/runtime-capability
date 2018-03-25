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

use Sjorek\RuntimeCapability\Capability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Capability\Configuration\ConfigurableTrait;
use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractDetector extends AbstractManageable implements DetectorInterface
{
    use ConfigurableTrait;

    /**
     * @var DetectorManagerInterface
     */
    protected $manager = null;

    /**
     * @var bool
     */
    protected $compactResult = false;

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setup()
     */
    public function setup()
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
        if ($this->compactResult) {
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
     * {@inheritdoc}
     *
     * @param DetectorManagerInterface $manager
     *
     * @return DetectorInterface
     *
     * @see DetectorInterface::setManager()
     */
    public function setManager(DetectorManagerInterface $manager): DetectorInterface
    {
        return parent::setManager($manager);
    }

    /**
     * Reduce the given array of capability detection results to the most compact value.
     *
     * @param array[bool[]] $capabilities
     */
    protected function reduceResult(array $capabilities)
    {
        return array_map(
            function ($capability) {
                if (false === $capability || true === $capability) {
                    return $capability;
                }
                if (!in_array(false, $capability, true)) {
                    return true;
                }
                if (in_array(true, $capability, true)) {
                    return $capability;
                }

                return false;
            },
            $capabilities
        );
    }
}
