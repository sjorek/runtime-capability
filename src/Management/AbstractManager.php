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
abstract class AbstractManager extends AbstractManageable implements ManagerInterface
{
    /**
     * @var string[]
     */
    protected $registry = [];

    /**
     * @var ManageableInterface[]
     */
    protected $instances = [];

    /**
     * @param ManageableInterface $instance
     *
     * @return ManageableInterface
     */
    public function registerManageable(ManageableInterface $instance): ManageableInterface
    {
        $id = $instance->setManager($this)->identify();
        $this->registry[$id] = get_class($instance);

        return $this->instances[$id] = $instance;
    }

    /**
     * @param string $idOrManageableClass
     *
     * @throws \InvalidArgumentException
     *
     * @return ManageableInterface
     */
    public function createManageable(string $idOrManageableClass): ManageableInterface
    {
        if (isset($this->instances[$idOrManageableClass])) {
            return $this->instances[$idOrManageableClass];
        }
        if (isset($this->registry[$idOrManageableClass])) {
            $idOrManageableClass = $this->registry[$idOrManageableClass];
        }
        if (!class_exists($idOrManageableClass, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The class does not exist: %s',
                    $idOrManageableClass
                ),
                1521207163
            );
        }
        if (!is_subclass_of($idOrManageableClass, ManageableInterface::class, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The class does implement the interface "%s": %s',
                    ManageableInterface::class,
                    $idOrManageableClass
                ),
                1521207167
            );
        }

        return $this->registerManageable(new $idOrManageableClass());
    }
}
