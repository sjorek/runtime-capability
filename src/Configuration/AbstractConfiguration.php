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
use Sjorek\RuntimeCapability\Management\ManageableInterface;
use Sjorek\RuntimeCapability\Management\ManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractConfiguration extends AbstractManageable implements ConfigurationInterface
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $manager = null;

    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritdoc}
     *
     * @param array $configuration
     *
     * @see ConfigurationInterface::__construct()
     */
    public function __construct(array $configuration = [])
    {
        if (!empty(array_filter(array_keys($configuration), function ($key) { return !is_string($key); }))) {
            throw new \InvalidArgumentException(
                'Invalid array given. Only keys of type string are allowed.',
                1522138977
            );
        }
        $this->data = $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationInterface::setConfigurationManager()
     */
    public function setConfigurationManager(ConfigurationManagerInterface $manager): ConfigurationInterface
    {
        return parent::setManager($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationInterface::getConfigurationManager()
     */
    public function getConfigurationManager(): ConfigurationManagerInterface
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
        return $this->setConfigurationManager($manager);
    }

    /**
     * @return ConfigurationManagerInterface
     */
    public function getManager(): ManagerInterface
    {
        return $this->getConfigurationManager();
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationInterface::export()
     */
    public function export(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationInterface::import()
     */
    public function import(ConfigurationInterface $configuration): ConfigurationInterface
    {
        $this->data = $configuration->export();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationInterface::merge()
     */
    public function merge(ConfigurationInterface $configuration): ConfigurationInterface
    {
        $this->data = array_merge($this->data, $configuration->export());

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        // if (isset($this->data[$offset])) {
        unset($this->data[$offset]);
        // }
    }
}
