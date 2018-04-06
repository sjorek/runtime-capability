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
use Sjorek\RuntimeCapability\Management\AbstractManageable;
use Sjorek\RuntimeCapability\Management\ManageableInterface;
use Sjorek\RuntimeCapability\Management\ManagerInterface;

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
     * @see DetectorInterface::setDetectorManager()
     */
    public function setDetectorManager(DetectorManagerInterface $manager): DetectorInterface
    {
        return parent::setManager($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @see DetectorInterface::getDetectorManager()
     */
    public function getDetectorManager(): DetectorManagerInterface
    {
        return parent::getManager();
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractManageable::setManager()
     */
    public function setManager(ManagerInterface $manager): ManageableInterface
    {
        return $this->setDetectorManager($manager);
    }

    /**
     * @return DetectorManagerInterface
     */
    public function getManager(): ManagerInterface
    {
        return $this->getDetectorManager();
    }

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
