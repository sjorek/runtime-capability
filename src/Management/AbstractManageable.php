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

use Sjorek\RuntimeCapability\Identification\AbstractIdentifiable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractManageable extends AbstractIdentifiable implements ManageableInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager = null;

    /**
     * @var ManagementInterface
     */
    protected $management = null;

    /**
     * {@inheritdoc}
     *
     * @see ManageableInterface::setManager()
     */
    public function setManager(ManagerInterface $manager): ManageableInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     * @return ManagerInterface
     * @see ManageableInterface::getManager()
     */
    public function getManager(): ManagerInterface
    {
        if (null !== $this->manager) {
            return $this->manager;
        }

        throw new \RuntimeException('Missing manager instance.', 1522098121);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     * @return ManagementInterface
     */
    public function getManagement(): ManagementInterface
    {
        if (null !== $this->management) {
            return $this->management;
        }

        $manager = $this;

        try {
            while (!$manager instanceof ManagementInterface) {
                $manager = $manager->getManager();
            }
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Missing management instance.', 1522098124);
        }

        return $this->management = $manager;
    }
}
