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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDriver extends AbstractFilesystemDriver implements FilesystemDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::create()
     */
    public function create($path)
    {
        return touch((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::exists()
     */
    public function exists($path)
    {
        return file_exists((string) $path) || is_link((string) $path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        return unlink((string) $path);
    }
}
