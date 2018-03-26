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

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ConfigurableInterface
{
    /**
     * @param array $configuration
     */
    public function setConfiguration(array &$configuration): self;

    /**
     * @return array
     */
    public function &getConfiguration(): array;

    /**
     * @return ConfigurableInterface
     */
    public function setup(): self;

    /**
     * @return ConfigurableInterface
     */
    public function reset(): self;

    /**
     * @param string[]    $keys
     * @param null|string $type
     * @param mixed       $key
     *
     * @return mixed
     */
    public function config($key, $type = null, ...$payload);
}
