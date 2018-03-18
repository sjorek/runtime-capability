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

use Sjorek\RuntimeCapability\Management\AbstractManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class CapabilityManager extends AbstractManager implements CapabilityManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param CapabilityInterface $capability
     *
     * @see AbstractManager::register()
     */
    public function register(CapabilityInterface $capability): CapabilityInterface
    {
        return parent::register($capability);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $idOrCapabilityClass
     *
     * @return CapabilityInterface
     *
     * @see AbstractManager::get()
     */
    public function get(string $idOrCapabilityClass): CapabilityInterface
    {
        return parent::get($idOrCapabilityClass);
    }
}
