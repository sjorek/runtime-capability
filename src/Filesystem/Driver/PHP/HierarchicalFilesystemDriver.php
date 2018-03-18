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

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\HierarchicalFilesystemDriverInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
class HierarchicalFilesystemDriver extends FlatFilesystemDriver implements HierarchicalFilesystemDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see FlatFilesystemDriver::setPath()
     */
    public function setPath($path = null)
    {
        $oldPath = $this->path;
        $newPath = parent::enter($path);
        if (!$this->isFolder($path)) {
            $this->path = $oldPath;
            throw new IOException(sprintf('Can not enter non-directory path: %s', $path), 1521216078);
        }

        return $newPath;
    }

    /**
     * {@inheritdoc}
     *
     * @see HierarchicalFilesystemDriverInterface::createFolder()
     */
    public function createFolder($path)
    {
        if ($this->exists($path)) {
            throw new IOException('Can not create folder, as it\'s path already exists.', 1521172856);
        }
        $this->fs->mkdir($this->canonical($path));

        return $path;
    }

    /**
     * {@inheritdoc}
     *
     * @see HierarchicalFilesystemDriverInterface::isFolder()
     */
    public function isFolder($path)
    {
        $path = $this->canonical($path);

        return $this->fs->exists($path) && is_dir($path) && !is_link($path);
    }
}
