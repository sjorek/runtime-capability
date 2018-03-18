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
use Sjorek\RuntimeCapability\Filesystem\Driver\FlatFilesystemDriverInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FlatFilesystemDriver extends AbstractFilesystemDriver implements FlatFilesystemDriverInterface, \IteratorAggregate
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $path = '.';

    /**
     * @var string
     */
    protected $iteratorPattern = '*';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::setPath()
     */
    public function setPath($path = null)
    {
        if (null === $path) {
            $path = false !== ($path = getcwd()) ? $path : '.';
        }
        if (!$this->exists($path)) {
            throw new IOException(sprintf('Can not enter non-existent path: %s', $path), 1521216071);
        }

        return $this->path = $this->concat($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::create()
     */
    public function create($path)
    {
        $this->fs->touch($this->canonical($path));

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::exists()
     */
    public function exists($path)
    {
        $path = $this->canonical($path);

        $this->clearstatcache($path);

        return $this->fs->exists($path) || is_link($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        $path = $this->canonical($path);
        $this->fs->remove($path);
        $this->clearstatcache($path);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::setIteratorPattern()
     */
    public function setIteratorPattern($pattern)
    {
        $this->iteratorPattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriverInterface::getIterator()
     */
    public function getIterator()
    {
        $pattern = $this->canonical($this->iteratorPattern);

        return function () use ($pattern) {
            foreach (glob($pattern, GLOB_NOSORT) as $path) {
                yield basename($path);
            }
        };
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function canonical(string $path)
    {
        return $this->concat(null !== $this->path ? $this->path : $this->enter(), $path);
    }

    /**
     * @param string[] ...$segments
     *
     * @return string
     */
    protected function concat(...$segments)
    {
        $prefix = !empty($segments) ? '/' : '';

        // Normalize separators on Windows
        if ('\\' === DIRECTORY_SEPARATOR) {
            $segments = array_map(
                function (string $segment) { return strtr($segment, '\\', '/'); },
                $segments
            );
            $s = $segments[0];
            // check if we have a drive-letter prepended
            if ('' !== $prefix && strlen($s) > 2 && ':' === $s[1] && '/' === $s[2] && ctype_alpha($s[0])) {
                // should be a drive letter (only supports single letter drive names!)
                $prefix = substr($s, 0, 3);
                $segments[0] = substr($s, 3);
            }
        }

        return implode(
            '/',
            array_map(
                function (int $index, string $segment) use ($prefix) {
                    return ('' !== ($segment = rtrim($segment, '/')) || 0 < $index) ? $segment : $prefix;
                },
                array_keys($segments),
                $segments
            )
        );
    }

    protected function clearstatcache(string $path)
    {
        clearstatcache(true, $path);
    }
}
