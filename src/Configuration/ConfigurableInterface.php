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

namespace Sjorek\RuntimeCapability\Capability\Configuration;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
interface ConfigurableInterface
{
    /**
     * @param array $configuration
     */
    public function setConfiguration(array & $configuration): self;

    /**
     * @return array
     */
    public function & getConfiguration() : array;

    /**
     * @return array
     */
    public function setup() : self;

    /**
     * @return array
     */
    public function reset() : self;

    /**
     * @param string[]    $keys
     * @param null|string $type
     *
     * @return mixed
     */
    public function config($key, $type = null, ...$payload);
 }
