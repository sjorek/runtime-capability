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

use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractConfiguration extends AbstractManageable implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritDoc}
     * @param array $configuration
     * @see ConfigurationInterface::__construct()
     */
    public function __construct(array $configuration = [])
    {
        if (!empty(array_filter(array_keys($configuration), function($key) { return !is_string($key); }))) {
            throw new \InvalidArgumentException(
                'Invalid array given. Only keys of type string are allowed.',
                1522138977
            );
        }
        $this->data = $configuration;
    }

    /**
     * {@inheritDoc}
     * @see ConfigurationInterface::export()
     */
    public function export(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     * @see ConfigurationInterface::import()
     */
    public function import(ConfigurationInterface $configuration): ConfigurationInterface
    {
        $this->data = $configuration->export();

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see ConfigurationInterface::merge()
     */
    public function merge(ConfigurationInterface $configuration): ConfigurationInterface
    {
        $this->data = array_merge($this->data, $configuration->export());

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * {@inheritDoc}
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritDoc}
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException(
                'Invalid offset given. Only keys of type string are allowed.',
                1522138977
            );
        }
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        // if (isset($this->data[$offset])) {
        unset($this->data[$offset]);
        // }
    }
}
