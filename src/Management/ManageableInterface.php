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

use Sjorek\RuntimeCapability\Identification\IdentifiableInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ManageableInterface extends IdentifiableInterface
{
    /**
     * @param ManagerInterface $manager
     *
     * @return ManageableInterface
     */
    public function setManager(ManagerInterface $manager): self;

    /**
     * @return ManagerInterface
     */
    public function getManager(): ManagerInterface;

    /**
     * @return ManagementInterface
     */
    public function getManagement(): ManagementInterface;
}
