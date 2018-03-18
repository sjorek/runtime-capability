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

namespace Sjorek\RuntimeCapability\Management;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface ManagerInterface
{
    /**
     * @param ManageableInterface $instance
     */
    public function register(ManageableInterface $instance): ManageableInterface;

    /**
     * @param string $idOrManagableClass
     *
     * @return ManageableInterface
     */
    public function get(string $idOrManagableClass): ManageableInterface;

    /**
     * @return ManagementInterface
     */
    public function getManagement(): ManagementInterface;
}
