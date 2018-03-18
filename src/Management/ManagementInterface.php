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

use Sjorek\RuntimeCapability\Capability\CapabilityManagerInterface;
use Sjorek\RuntimeCapability\Capability\Detection\DetectorManagerInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FilesystemDriverManagerInterface;

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

    /**
     * @return ManagementInterface
     */
    public function getManagement(): self;

    /**
     * @return CapabilityManagerInterface
     */
    public function getCapabilityManager() : CapabilityManagerInterface;

    /**
     * @return DetectorManagerInterface
     */
    public function getDetectorManager() : DetectorManagerInterface;

    /**
     * @return FilesystemDriverManagerInterface
     */
    public function getFilesystemDriverManager() : FilesystemDriverManagerInterface;
}
