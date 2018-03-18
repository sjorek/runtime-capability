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

namespace Sjorek\RuntimeCapability\Filesystem\Driver;

use Sjorek\RuntimeCapability\Management\ManagerInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemDriverManagerInterface extends ManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param FilesystemDriverInterface $driver
     *
     * @return FilesystemDriverInterface
     *
     * @see ManagerInterface::register()
     */
    public function register(FilesystemDriverInterface $driver): FilesystemDriverInterface;

    /**
     * {@inheritdoc}
     *
     * @param string $idOrFilesystemDriverClass
     *
     * @return FilesystemDriverInterface
     *
     * @see ManagerInterface::get()
     */
    public function get(string $idOrFilesystemDriverClass): FilesystemDriverInterface;
}
