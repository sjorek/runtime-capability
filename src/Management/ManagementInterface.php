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
interface ManagementInterface
{
    /**
     * @param ManagerInterface $instance
     */
    public function registerManager(ManagerInterface $instance): ManagerInterface;

    /**
     * @param string $idOrManagableClass
     *
     * @return ManagerInterface
     */
    public function createManager(string $idOrManagerClass): ManagerInterface;
}
