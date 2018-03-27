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

use Sjorek\RuntimeCapability\Management\AbstractManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class ConfigurationManager extends AbstractManager implements ConfigurationManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see ConfigurationManagerInterface::registerConfiguration()
     */
    public function registerConfiguration(ConfigurationInterface $instance): ConfigurationInterface
    {
        return $this->registerManageable($instance);
    }

    /**
     * {@inheritdoc}
     *
     * @see ConfigurationManagerInterface::createConfiguration()
     */
    public function createConfiguration(string $idOrConfigurationClass): ConfigurationInterface
    {
        return $this->createManageable($idOrConfigurationClass);
    }
}
