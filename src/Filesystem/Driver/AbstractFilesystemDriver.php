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
     * @var int
     */
    const MAXIMUM_PATH_LENGTH = PHP_MAXPATHLEN;

    /**
     * @var string
     */
    const DETECT_PATH_TRAVERSAL_PATTERN = '#(?:^|[/\\\\])\.\.(?:[/\\\\]|$)#u';

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
        $pathLength = static::MAXIMUM_PATH_LENGTH;
        if ('\\' === DIRECTORY_SEPARATOR) {
            // subtract 2 for windows drive letter plus double colon, like 'c:'
            $pathLength -= 2;
        }

        return $pathLength;
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
     * Validate path-length and -integrity.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException if path is empty, exceeds maximum length or contains traversals ("..")
     */
    protected function validatePath(string $path): bool
    {
        if ('' === $path) {
            throw new \InvalidArgumentException(
                'Invalid path given. The path is empty ("").',
                1522171135
            );
        }

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

        if (preg_match(self::DETECT_PATH_TRAVERSAL_PATTERN, $path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s. Path traversal ("..") is not allowed.', $path),
                1522171140
            );
        }

        return true;
    }
}
