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

namespace Sjorek\RuntimeCapability\Management;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractManagement extends AbstractManager implements ManagementInterface
{
    /**
     * {@inheritDoc}
     *
     * @see ManagementInterface::registerManager()
     */
    public function registerManager(ManagerInterface $instance): ManagerInterface
    {
        return $this->registerManageable($instance);
    }

    /**
     * {@inheritDoc}
     *
     * @see ManagementInterface::createManager()
     */
    public function createManager(string $idOrManagerClass): ManagerInterface
    {
        return $this->createManageable($idOrManagerClass);
    }
}