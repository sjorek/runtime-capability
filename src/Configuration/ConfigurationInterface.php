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

use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ConfigurationInterface extends \ArrayAccess, ManageableInterface
{
    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = []);

    /**
     * @return array
     */
    public function export(): array;

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return self
     */
    public function import(self $configuration): self;

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return self
     */
    public function merge(self $configuration): self;

    /**
     * @param ConfigurationManagerInterface $manager
     *
     * @return self
     */
    public function setConfigurationManager(ConfigurationManagerInterface $manager): self;

    /**
     * @return ConfigurationManagerInterface
     */
    public function getConfigurationManager(): ConfigurationManagerInterface;
}
