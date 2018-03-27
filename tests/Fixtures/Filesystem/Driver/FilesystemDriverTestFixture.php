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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver;

use Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDriverTestFixture extends AbstractFilesystemDriver
{
    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::create()
     */
    public function create($path)
    {
        return touch($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::exists()
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        return unlink($path);
    }
}
