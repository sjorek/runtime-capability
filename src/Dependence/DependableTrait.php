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

namespace Sjorek\RuntimeCapability\Dependence;

use Sjorek\RuntimeCapability\Management\ManageableInterface;
use Sjorek\RuntimeCapability\Management\ManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait DependableTrait
{
    /**
     * @var DependencyManagerInterface
     */
    protected $manager = null;

    /**
     * {@inheritdoc}
     *
     * @see DependableInterface::setDependencyManager()
     */
    public function setDependencyManager(DependencyManagerInterface $manager): DependableInterface
    {
        return parent::setManager($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @see DependableInterface::setDependencyManager()
     */
    public function getDependencyManager(): DependencyManagerInterface
    {
        return parent::getManager();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Management\AbstractManageable::setManager()
     */
    public function setManager(ManagerInterface $manager): ManageableInterface
    {
        return $this->setDependencyManager($manager);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Management\AbstractManageable::getManager()
     */
    public function getManager(): ManagerInterface
    {
        return $this->getDependencyManager();
    }
}
