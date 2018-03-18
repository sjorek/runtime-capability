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

namespace Sjorek\RuntimeCapability\Capability;

use Sjorek\RuntimeCapability\Management\ManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface CapabilityManagerInterface extends ManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param CapabilityInterface $capability
     *
     * @return CapabilityInterface
     *
     * @see ManagerInterface::register()
     */
    public function register(CapabilityInterface $capability): CapabilityInterface;

    /**
     * {@inheritdoc}
     *
     * @param string $idOrCapabilityClass
     *
     * @return CapabilityInterface
     *
     * @see ManagerInterface::get()
     */
    public function get(string $idOrCapabilityClass): CapabilityInterface;
}
