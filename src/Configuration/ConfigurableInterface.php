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

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ConfigurableInterface
{
    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration): self;

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * @param string      $key
     * @param null|string $type
     *
     * @return mixed
     */
    public function config(string $key, string $type = null);

    /**
     * @return ConfigurableInterface
     */
    public function setup(): self;

    /**
     * @return ConfigurableInterface
     */
    public function reset(): self;
}
