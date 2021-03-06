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

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface FilesystemDriverManagerInterface
{
    /**
     * @param FilesystemDriverInterface $driver
     *
     * @return FilesystemDriverInterface
     */
    public function registerFilesystemDriver(FilesystemDriverInterface $driver): FilesystemDriverInterface;

    /**
     * @param string $idOrFilesystemDriverClass
     *
     * @return FilesystemDriverInterface
     */
    public function createFilesystemDriver(string $idOrFilesystemDriverClass): FilesystemDriverInterface;
}
