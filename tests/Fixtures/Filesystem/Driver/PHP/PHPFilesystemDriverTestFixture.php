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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PHPFilesystemDriverTestFixture extends AbstractPHPFilesystemDriver
{
    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::createTarget()
     */
    public function createTarget($path): bool
    {
        return touch($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::targetExists()
     */
    public function targetExists($path): bool
    {
        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface::removeTarget()
     */
    public function removeTarget($path): bool
    {
        return unlink($path);
    }
}
