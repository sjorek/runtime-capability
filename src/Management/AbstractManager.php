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

use Sjorek\RuntimeCapability\Exception\CapabilityDetectionFailure;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractManager extends AbstractManageable implements ManagerInterface
{
    /**
     * @var ManagementInterface
     */
    protected $management;

    /**
     * @var ManageableInterface[]
     */
    protected $instances = [];

    /**
     * @param ManageableInterface $instance
     */
    public function register(ManageableInterface $instance): ManageableInterface
    {
        return $this->instances[$instance->identify()] = $instance;
    }

    public function get(string $idOrManagableClass): ManageableInterface
    {
        if (isset($this->instances[$idOrManagableClass])) {
            return $this->instances[$idOrManagableClass];
        }
        if (!class_exists($idOrManagableClass, true)) {
            throw new CapabilityDetectionFailure(
                sprintf(
                    'The implementation does not exist: %s',
                    $idOrManagableClass
                ),
                1521207163
            );
        }
        if (!is_subclass_of($idOrManagableClass, ManageableInterface::class, true)) {
            throw new CapabilityDetectionFailure(
                sprintf(
                    'The class does implement the interface "%s": %s',
                    ManageableInterface::class,
                    $idOrManagableClass
                ),
                1521207167
            );
        }

        return $this->register(new $idOrManagableClass())->setManager($this);
    }

    /**
     * {@inheritdoc}
     *
     * @see ManagerInterface::getManagement()
     */
    public function getManagement(): ManagementInterface
    {
        return $this->manager;
    }
}
