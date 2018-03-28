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

use Sjorek\RuntimeCapability\Filesystem\Driver\PathFilesystemDriverInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PathFilesystemDriver extends FilesystemDriver implements PathFilesystemDriverInterface, \IteratorAggregate
{
    /**
     * @var string
     */
    protected $path = '.';

    /**
     * @var string
     */
    protected $iteratorPattern = '*';

    /**
     * {@inheritdoc}
     *
     * @see PathFilesystemDriverInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     * @see PathFilesystemDriverInterface::setPath()
     */
    public function setPath($path = null)
    {
        if (null === $path) {
            $path = $this->path;
        } elseif (!$this->isAbsolutePath($path)) {
            $path = $this->prependPath($path);
        }

        $path = $this->normalizePath($path);
        $this->validatePathLength($path);

        // the existence-checks are reliable in executable-directories only
        if (!($this->pathExists($path) && $this->isAccessibleDirectory($path) && $this->isWritableDirectory($path))) {
            return false;
        }

        return $this->path = $path;
    }

    /**
     * {@inheritDoc}
     * @see FilesystemDriver::create()
     */
    public function create($path)
    {
        return parent::create($this->prependPath($path));
    }

    /**
     * {@inheritDoc}
     * @see FilesystemDriver::exists()
     */
    public function exists($path)
    {
        $path = $this->prependPath($path);
        $this->cleanup($path);
        return parent::exists($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see PathFilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        $path = $this->prependPath($path);
        $this->isWritableDirectory($path);
        $this->cleanup($path);
        return parent::remove($this->prependPath($path));
    }

    /**
     * {@inheritdoc}
     *
     * @see PathFilesystemDriverInterface::setIteratorPattern()
     */
    public function setIteratorPattern($pattern)
    {
        $this->iteratorPattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @see PathFilesystemDriverInterface::getIterator()
     */
    public function getIterator()
    {
        $pattern = $this->prependPath($this->iteratorPattern);
        $pattern = $this->normalizePath($pattern);
        $this->validatePathLength($pattern);

        return function () use ($pattern) {
            foreach (glob($pattern, GLOB_NOSORT) as $path) {
                yield basename($path);
            }
        };
    }

    /**
     * @param string path
     *
     * @return string
     */
    protected function prependPath(string $path): string
    {
        if ($this->isUrl($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s.', $path),
                1522171140
            );
        }

        return $this->path . '/' . $path;
    }

    /**
     * @param string $path
     * @throws \InvalidArgumentException if the path represents a malformed url
     * @return boolean
     */
    protected function isAbsolutePath(string $path): bool
    {
        if ('' === $path) {
            return false;
        }

        if ($this->isAbsoluteUrl($path)) {
            return true;
        }

        if ($this->isUrl($path)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid path given: %s.', $path),
                1522171140
            );
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            return
                2 < strlen($path) && ':' === $path[1] &&
                ('/' === $path[2] || '\\' === $path[2]) &&
                ctype_alpha($path[0])
            ;
        }

        return '/' === $path[0];
    }

    /**
     * Check if the path represents an absolute url - scheme and path are required.
     *
     * @param string $path
     * @return boolean
     */
    protected function isAbsoluteUrl(string $path): bool
    {
        return
            '' !== $path &&
            filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_PATH_REQUIRED)
        ;
    }

    /**
     * Check if the path represents an url - a scheme is required.
     *
     * @param string $path
     * @return boolean
     */
    protected function isUrl(string $path): bool
    {
        return
            '' !== $path &&
            filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)
        ;
    }

    /**
     * Normalize the given path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  CWD             # replace single dot with current working directory
     * ./test       =>  CWD/test        # replace leading dot with current working directory
     * .\test       =>  CWD/test        # replace leading dot with current working directory and turn windows backslash into slash
     * test/file    =>  CWD/test/file   # prepend current working directory to relative path
     * test\file    =>  CWD/test/file   # keep windows path without leading dot relative, still converting to slash
     * </pre>
     *
     * @param string $path
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        if ('' === $path || $this->isUrl($path)) {
            return $path;
        }

        $path = strtr($path, '\\', '/');

        if ('.' === $path || (1 < strlen($path) && '.' === $path[0] && '/' === $path[1])) {
            return $this->getCurrentWorkingDirectory() . substr($path, 1);
        }

        if (!$this->isAbsolutePath($path)) {
            return $this->getCurrentWorkingDirectory() . '/' . $path;
        }

        return $path;
    }

    /**
     * @return string
     */
    protected function getCurrentWorkingDirectory(): string
    {
        if (false !== ($cwd = getcwd())) {
            return rtrim(strtr($cwd, '\\', '/'), '/');
        }

        return false !== ($cwd = realpath('.')) ? $cwd : '.';
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function isAccessibleDirectory(string $path): bool
    {
        return
            $this->isDirectory($path) &&
            (
                // TODO Find out why is_executable() fails for some vfs-directories
                is_executable($path) ||
                (
                    // TODO Remove the fileperms() workaround for vfs-directories
                    // @see http://php.net/manual/en/function.fileperms.php#example-2671
                    ($permissions = (@fileperms($path) ?: 0)) &&
                    (
                        ($permissions & 0x0040) && !($permissions & 0x0800) || // owner executable flag - [u]ser
                        ($permissions & 0x0008) && !($permissions & 0x0400) || // group executable flag - [g]roup
                        ($permissions & 0x0001) && !($permissions & 0x0200)    // world executable flag - [o]ther
                    )
                )
            )
        ;
    }

    /**
     * @param string $path
     * @return boolean
     */
    protected function isWritableDirectory(string $path): bool
    {
        return $this->isDirectory($path) && is_writable($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isDirectory(string $path): bool
    {
        return file_exists($path) && is_dir($path) && !is_link($path);
    }

    /**
     * @param string $path
     */
    protected function cleanup(string $path)
    {
        if (!$this->isUrl($path)) {
            clearstatcache(true, $path);
        }
    }
}
