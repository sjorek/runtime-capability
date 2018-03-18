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

namespace Sjorek\RuntimeCapability\Capability\Configuration;

use Sjorek\RuntimeCapability\Exception\CapabilityDetectionFailure;
use Sjorek\RuntimeCapability\Utility\ConfigurationUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait ConfigurableTrait
{
    /**
     * @var array
     */
    protected static $DEFAULT_CONFIGURATION = [];

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setConfiguration()
     */
    public function setConfiguration(array &$configuration): ConfigurableInterface
    {
        $this->configuration = &$configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::getConfiguration()
     */
    public function &getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setUp()
     */
    public function setup(): ConfigurableInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::reset()
     */
    public function reset(): ConfigurableInterface
    {
        $this->configuration = [];
    }

    /**
     * @param string[]    $keys
     * @param null|string $type
     * @param mixed       $key
     *
     * @return mixed
     */
    public function config($key, $type = null)
    {
        $key = $this->normalizeId($key);
        $id = strtolower(sprintf('%s.%s', $this->identify(), $key));
        $value = null;
        $found = false;
        foreach ([$id, $key] as $lookup) {
            if (isset($this->configuration[$lookup])) {
                $value = $this->configuration[$lookup];
                $found = true;
                break;
            }
            if (isset(static::$DEFAULT_CONFIGURATION[$lookup])) {
                $value = static::$DEFAULT_CONFIGURATION[$lookup];
                $found = true;
                break;
            }
            if (isset(self::$DEFAULT_CONFIGURATION[$lookup])) {
                $value = self::$DEFAULT_CONFIGURATION[$lookup];
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new CapabilityDetectionFailure(
                sprintf('Missing configuration for key: %s (%s).', $key, $id),
                1521291482
            );
        }
        if (null !== $type && $type !== ($actual = ConfigurationUtility::getTypeForValue($type, $value))) {
            throw new CapabilityDetectionFailure(
                sprintf(
                    'Invalid configuration value type for key "%s", expected type "%s", but got type "%s".',
                    $key,
                    $type,
                    $actual
                ),
                1521291487
            );
        }

        return $value;
    }
}
