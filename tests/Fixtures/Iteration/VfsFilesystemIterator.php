<?php
namespace Sjorek\RuntimeCapability\Tests\Fixtures\Iteration;

/**
 * vfs-specific FilesystemIterator implementation
 */
class VfsFilesystemIterator extends \FilesystemIterator
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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

