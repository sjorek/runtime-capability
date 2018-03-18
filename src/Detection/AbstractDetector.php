<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Detection;

use Sjorek\RuntimeCapability\Exception\CapabilityDetectionFailure;
use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractDetector extends AbstractManageable implements DetectorInterface
{
    /**
     * @var DetectorManagerInterface
     */
    protected $manager = null;

    /**
     * @var array
     */
    protected static $DEFAULT_CONFIGURATION = [
        'compact-result' => true,
    ];

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var bool
     */
    protected $compactResult = false;

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
     * @see DetectorInterface::setup()
     */
    public function setup(array &$configuration)
    {
        $this->configuration = &$configuration;
        $this->compactResult = $this->getConfiguration('compact-result', 'boolean');
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
     * @param string[]    $keys
     * @param null|string $type
     * @param mixed       $key
     *
     * @return mixed
     */
    protected function getConfiguration($key, $type = null)
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
        if (null !== $type && $type !== ($actual = gettype($value))) {
            throw new CapabilityDetectionFailure(
                sprintf(
                    'Invalid configuration value type for key "%s": expected type "%s", but got type "%s".',
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
