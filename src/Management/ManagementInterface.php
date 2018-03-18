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
interface ManagementInterface extends ManagerInterface
{
    /**
     * @param ManagerInterface $manager
     */
    public function register(ManagerInterface $manager): ManagerInterface;

    /**
     * @param string $idOrManagableClass
     *
     * @return ManagerInterface
     */
    public function get(string $idOrManagerClass): ManagerInterface;
}
