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
class FilesystemDriver extends AbstractFilesystemDriver implements PhpDrivenFilesystemDriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::create()
     */
    public function create($path)
    {
        $this->validatePathLength($path);

        return (!$this->pathExists($path)) && touch($path);
    }

    /**
     * Attention: the existence-check is reliable in executable-directories only
     *
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::exists()
     */
    public function exists($path)
    {
        $this->validatePathLength($path);

        return $this->pathExists($path);
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDriverInterface::remove()
     */
    public function remove($path)
    {
        $this->validatePathLength($path);

        return $this->isFile($path) && unlink($path);
    }

    /**
     * Check for path existence, no matter which kind of entry the path points to.
     * Uses an additional is_link() check to ensure capturing existing dangling symlinks.
     *
     * @param string $path
     * @return bool
     */
    protected function pathExists(string $path): bool
    {
        return file_exists($path) || is_link($path);
    }

    /**
     * Check for file existence - which means only files.
     * Uses an additional is_link() check to ensure capturing symlinks.
     *
     * @param string $path
     * @return bool
     */
    protected function isFile(string $path): bool
    {
        return file_exists($path) && is_file($path) && !is_link($path);
    }
}
