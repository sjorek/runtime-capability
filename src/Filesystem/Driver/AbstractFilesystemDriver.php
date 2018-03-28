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
     * @var integer
     */
    const MAXIMUM_PATH_LENGTH = PHP_MAXPATHLEN;

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
        // subtract 2 for windows drive letter plus double colon, like 'c:'
        return static::MAXIMUM_PATH_LENGTH - 2;
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

    /**
     * @param string $path
     * @throws \InvalidArgumentException if the path exceeds the maximum path length
     */
    protected function validatePathLength(string $path)
    {
        if (!$this->hasValidPathLength($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The path exceeds the maximum path length of %s bytes.',
                    $path,
                    $this->getMaximumPathLength()
                ),
                1522171138
            );
        }
    }
}
