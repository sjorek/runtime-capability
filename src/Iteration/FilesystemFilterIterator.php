<?php
namespace Sjorek\RuntimeCapability\Iteration;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemFilterIterator extends \FilterIterator
{
    /**
     * @var integer
     */
    const ACCEPT_FILE = 1;

    /**
     * @var integer
     */
    const ACCEPT_DIRECTORY = 2;

    /**
     * @var integer
     */
    const ACCEPT_LINK = 4;

    /**
     * @var int
     */
    const DEFAULT_FLAGS =
        self::ACCEPT_FILE |
        self::ACCEPT_DIRECTORY |
        self::ACCEPT_LINK
    ;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $flags;

    /**
     * @var \Closure
     */
    protected $getFileType;

    /**
     * @param \FilesystemIterator $iterator
     * @param int $flags
     */
    public function __construct(\FilesystemIterator $iterator, int $flags = self::DEFAULT_FLAGS)
    {
        $this->flags = $flags;
        $this->getFileType = $this->createGetFileTypeClosure($iterator->getFlags());

        parent::__construct($iterator);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException
     * @see \FilterIterator::accept()
     */
    public function accept()
    {
        $getFileType = $this->getFileType;

        switch($getFileType($this->current()))
        {
            case 'file':
                return $this->flags & self::ACCEPT_FILE;
            case 'dir':
                return $this->flags & self::ACCEPT_DIRECTORY;
            case 'link':
                return $this->flags & self::ACCEPT_LINK;
        }

        return false;
    }

    /**
     * @param int $flags
     * @throws \RuntimeException
     * @return \Closure
     */
    protected function createGetFileTypeClosure(int $flags): \Closure
    {
        $flags &= \FileSystemIterator::CURRENT_MODE_MASK;
        if (\FilesystemIterator::CURRENT_AS_FILEINFO === $flags) {
            return function(\SplFileInfo $fileInfo) {
                return $fileInfo->getType();
            };
        }

        if(\FilesystemIterator::CURRENT_AS_SELF === $flags) {
            return function(\FilesystemIterator $iterator) {
                return $iterator->getType();
            };
        }

        if(\FilesystemIterator::CURRENT_AS_PATHNAME === $flags) {
            return function(string $path) {
                return (new \SplFileInfo($path))->getType();
            };
        }

        throw new \RuntimeException(sprintf('Failed to decode iterator flags: %b', $flags), 1522919722);
    }
}