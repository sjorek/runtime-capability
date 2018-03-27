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

use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractFilesystemDriver extends AbstractManageable implements FilesystemDriverInterface
{
    /**
     * @var FilesystemDriverManagerInterface
     */
    protected $manager = null;

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::getMaximumPathLength()
     */
    public function getMaximumPathLength(): int
    {
        return PHP_MAXPATHLEN - 2;
    }

    /**
     * Validate the given path (including filename) against the driver's maximum path length.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function hasValidPathLength(string $path): bool
    {
        return !('' === $path || strlen($path) > $this->getMaximumPathLength());
    }
}
