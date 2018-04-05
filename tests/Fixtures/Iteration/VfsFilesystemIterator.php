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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Iteration;

/**
 * vfs-specific FilesystemIterator implementation.
 */
class VfsFilesystemIterator extends \FilesystemIterator
{
    /**
     * {@inheritdoc}
     *
     * @see \FilesystemIterator::getType()
     */
    public function getType()
    {
        if ($this->isLink()) {
            return 'link';
        }

        return parent::getType();
    }

    /**
     * {@inheritdoc}
     *
     * @see \FilesystemIterator::isFile()
     */
    public function isFile()
    {
        if ($this->isLink()) {
            return false;
        }

        return parent::isFile();
    }

    /**
     * {@inheritdoc}
     *
     * @see \FilesystemIterator::isDir()
     */
    public function isDir()
    {
        if ($this->isLink()) {
            return false;
        }

        return parent::isDir();
    }

    /**
     * {@inheritdoc}
     *
     * @see \FilesystemIterator::isFile()
     */
    public function isLink()
    {
        if (0 === strpos($this->getPath(), 'vfs://') && false !== strpos($this->getFilename(), 'symlink')) {
            return true;
        }

        return parent::isLink();
    }
}
