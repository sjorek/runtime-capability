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

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Driver;

use Sjorek\RuntimeCapability\Management\AbstractManager;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDriverManager extends AbstractManager implements FilesystemDriverManagerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param FilesystemDriverInterface $driver
     *
     * @return FilesystemDriverInterface
     *
     * @see AbstractManager::register()
     */
    public function register(FilesystemDriverInterface $driver): FilesystemDriverInterface
    {
        return parent::register($driver);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $idOrFilesystemDriverClass
     *
     * @return FilesystemDriverInterface
     *
     * @see AbstractManager::get()
     */
    public function get(string $idOrFilesystemDriverClass): FilesystemDriverInterface
    {
        return parent::get($idOrFilesystemDriverClass);
    }
}
