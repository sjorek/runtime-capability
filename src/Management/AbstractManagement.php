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
     * {@inheritdoc}
     *
     * @param ManagerInterface $manager
     *
     * @return ManagerInterface
     *
     * @see AbstractManager::register()
     */
    public function register(ManagerInterface $manager): ManagerInterface
    {
        return parent::register($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $idOrManagerClass
     *
     * @return ManagerInterface
     *
     * @see AbstractManager::get()
     */
    public function get(string $idOrManagerClass): ManagerInterface
    {
        return parent::get($idOrManagerClass);
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractManager::getManagement()
     */
    public function getManagement(): ManagementInterface
    {
        return $this;
    }
}
