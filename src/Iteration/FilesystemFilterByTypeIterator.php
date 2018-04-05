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

namespace Sjorek\RuntimeCapability\Iteration;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemFilterByTypeIterator extends \FilterIterator
{
    /**
     * @var int
     */
    const ACCEPT_NONE = 0;

    /**
     * @var int
     */
    const ACCEPT_FILE = 1;

    /**
     * @var int
     */
    const ACCEPT_DIRECTORY = 2;

    /**
     * @var int
     */
    const ACCEPT_LINK = 4;

    /**
     * @var int
     */
    const ACCEPT_ALL =
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
     * @var bool[]
     */
    protected $types = [];

    /**
     * @var \Closure
     */
    protected $getFileType;

    /**
     * @param \FilesystemIterator $iterator
     * @param int                 $flags
     */
    public function __construct(\FilesystemIterator $iterator, int $flags = self::ACCEPT_ALL)
    {
        $this->setFlags($flags);
        $this->getFileType = $this->createGetFileTypeClosure($iterator->getFlags());

        parent::__construct($iterator);
    }

    /**
     * @param int $flags
     */
    public function setFlags(int $flags)
    {
        $this->flags = $flags;
        $this->types = [
            'file' => self::ACCEPT_NONE !== ($flags & self::ACCEPT_FILE),
            'dir' => self::ACCEPT_NONE !== ($flags & self::ACCEPT_DIRECTORY),
            'link' => self::ACCEPT_NONE !== ($flags & self::ACCEPT_LINK),
        ];
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     *
     * @see \FilterIterator::accept()
     */
    public function accept()
    {
        $getFileType = $this->getFileType;

        return $this->types[$getFileType($this->current())] ?? false;
    }

    /**
     * @param int $flags
     *
     * @throws \RuntimeException
     *
     * @return \Closure
     */
    protected function createGetFileTypeClosure(int $flags): \Closure
    {
        $mode = $flags & \FileSystemIterator::CURRENT_MODE_MASK;

        if (\FilesystemIterator::CURRENT_AS_PATHNAME === $mode) {
            return function (string $path): string {
                return (new \SplFileInfo($path))->getType();
            };
        }

        if (\FilesystemIterator::CURRENT_AS_FILEINFO === $mode) {
            return function (\SplFileInfo $fileInfo): string {
                return $fileInfo->getType();
            };
        }

        if (\FilesystemIterator::CURRENT_AS_SELF === $mode) {
            return function (\FilesystemIterator $iterator): string {
                return $iterator->getType();
            };
        }

        throw new \InvalidArgumentException(
            sprintf('Failed to decode iterator flags: %s (%b)', $flags, $flags),
            1522919722
        );
    }
}
