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

namespace Sjorek\RuntimeCapability\Configuration;

use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Utility\ConfigurationUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait ConfigurableTrait
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setConfiguration()
     */
    public function setConfiguration(ConfigurationInterface $configuration): ConfigurableInterface
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::getConfiguration()
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @param string   $key
     * @param string[] $types
     *
     * @throws ConfigurationFailure
     *
     * @return mixed
     *
     * @see ConfigurableInterface::setup()
     */
    public function config(string $key, string ...$types)
    {
        $key = $this->normalizeIdentifier($key);
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
            throw new ConfigurationFailure(
                sprintf('Missing configuration for key: %s (%s).', $key, $id),
                1521291482
            );
        }

        if (empty($types)) {
            return $value;
        }

        foreach ($types as $type) {
            $actual = ConfigurationUtility::getTypeForValue($type, $value);
            if ($type === $actual) {
                continue;
            }
            throw new ConfigurationFailure(
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

    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setup()
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
        $this->configuration = null;

        return $this;
    }
}
