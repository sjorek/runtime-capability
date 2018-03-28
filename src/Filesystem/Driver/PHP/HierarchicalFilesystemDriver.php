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

use Sjorek\RuntimeCapability\Filesystem\Driver\HierarchicalFilesystemDriverInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
class HierarchicalFilesystemDriver extends PathFilesystemDriver implements HierarchicalFilesystemDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see PathFilesystemDriver::setPath()
     */
    public function setPath($path = null)
    {
        $oldPath = $this->path;
        $newPath = parent::setPath($path);
        if ($this->isFolder($path)) {
            return $newPath;
        }

        $this->path = $oldPath;
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see HierarchicalFilesystemDriverInterface::createFolder()
     */
    public function createFolder($path)
    {
        $path = $this->prependPath($path);
        $path = $this->normalizePath($path);
        $this->validatePathLength($path);

        if (!$this->exists($path) && $this->hasWritableParentDirectory($path) && mkdir($path, true)) {
            return $path;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see HierarchicalFilesystemDriverInterface::createFolder()
     */
    public function removeFolder($path)
    {
        $path = $this->prependPath($path);
        $path = $this->normalizePath($path);
        $this->validatePathLength($path);

        if ($this->exists($path) && is_dir($path) && $this->hasWritableParentDirectory($path) && rmdir($path)) {
            $this->cleanup($path);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see HierarchicalFilesystemDriverInterface::isFolder()
     */
    public function isFolder($path)
    {
        $path = $this->canonical($path);

        return $this->exists($path) && is_dir($path) && !is_link($path);
    }
}
