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

use Sjorek\RuntimeCapability\Management\AbstractManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDriverManager extends AbstractManager implements FilesystemDriverManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverManagerInterface::registerFilesystemDriver()
     */
    public function registerFilesystemDriver(FilesystemDriverInterface $driver): FilesystemDriverInterface
    {
        return $this->registerManageable($driver);
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverManagerInterface::createFilesystemDriver()
     */
    public function createFilesystemDriver(string $idOrFilesystemDriverClass): FilesystemDriverInterface
    {
        return $this->createManageable($idOrFilesystemDriverClass);
    }
}
