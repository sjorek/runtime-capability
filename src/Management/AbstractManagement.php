<?php
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
     * {@inheritDoc}
     *
     * @see AbstractManager::getManagement()
     */
    public function getManagement(): ManagementInterface
    {
        return $this;
    }
}

