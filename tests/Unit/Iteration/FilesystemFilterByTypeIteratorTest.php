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

namespace Sjorek\RuntimeCapability\Unit\Iteration;

use Sjorek\RuntimeCapability\Iteration\FilesystemFilterByTypeIterator;
use Sjorek\RuntimeCapability\Tests\Fixtures\Iteration\VfsFilesystemIterator;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * FilesystemFilterByTypeIterator test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Iteration\FilesystemFilterByTypeIterator
 */
class FilesystemFilterByTypeIteratorTest extends AbstractFilesystemTestCase
{
    /**
     * @var int
     */
    const FILESYSTEM_ITERATOR_COMMON_FLAGS =
        \FilesystemIterator::KEY_AS_PATHNAME |
        \FilesystemIterator::SKIP_DOTS |
        \FilesystemIterator::UNIX_PATHS
    ;

    /**
     * @var int
     */
    const FILESYSTEM_ITERATOR_USE_PATHNAME_FLAGS =
        self::FILESYSTEM_ITERATOR_COMMON_FLAGS |
        \FilesystemIterator::CURRENT_AS_PATHNAME
    ;

    /**
     * @var int
     */
    const FILESYSTEM_ITERATOR_USE_FILEINFO_FLAGS =
        self::FILESYSTEM_ITERATOR_COMMON_FLAGS |
        \FilesystemIterator::CURRENT_AS_FILEINFO
    ;

    /**
     * @var int
     */
    const FILESYSTEM_ITERATOR_USE_SELF_FLAGS =
        self::FILESYSTEM_ITERATOR_COMMON_FLAGS |
        \FilesystemIterator::CURRENT_AS_SELF
    ;

    /**
     * @covers ::__construct
     *
     * @return FilesystemFilterByTypeIterator
     */
    public function testConstruct(): FilesystemFilterByTypeIterator
    {
        $iterator = new FilesystemFilterByTypeIterator(
            new VfsFilesystemIterator($this->getFilesystem()->url(), self::FILESYSTEM_ITERATOR_USE_SELF_FLAGS)
        );
        $this->assertAttributeInternalType('integer', 'flags', $iterator);
        $this->assertAttributeInternalType('array', 'types', $iterator);
        $this->assertAttributeContainsOnly('boolean', 'types', $iterator);

        return $iterator;
    }

    /**
     * @covers ::getFlags
     * @depends testConstruct
     *
     * @param FilesystemFilterByTypeIterator $iterator
     *
     * @return FilesystemFilterByTypeIterator
     */
    public function testGetFlags(FilesystemFilterByTypeIterator $iterator): FilesystemFilterByTypeIterator
    {
        $this->assertSame(FilesystemFilterByTypeIterator::ACCEPT_ALL, $iterator->getFlags());
        $this->assertAttributeSame($this->createTypes(true, true, true), 'types', $iterator);

        return $iterator;
    }

    /**
     * @covers ::setFlags
     * @depends testGetFlags
     * @dataProvider provideTestSetFlagsData
     *
     * @param FilesystemFilterByTypeIterator $iterator
     */
    public function testSetFlags(int $flags, array $types, FilesystemFilterByTypeIterator $iterator)
    {
        $iterator->setFlags($flags);
        $this->assertAttributeSame($flags, 'flags', $iterator);
        $this->assertAttributeSame($types, 'types', $iterator);
    }

    /**
     * @return array
     */
    public function provideTestSetFlagsData(): array
    {
        return [
            'nothing' => [
                FilesystemFilterByTypeIterator::ACCEPT_NONE,
                $this->createTypes(false, false, false),
            ],
            'files' => [
                FilesystemFilterByTypeIterator::ACCEPT_FILE,
                $this->createTypes(true, false, false),
            ],
            'directories' => [
                FilesystemFilterByTypeIterator::ACCEPT_DIRECTORY,
                $this->createTypes(false, true, false),
            ],
            'symbolic links' => [
                FilesystemFilterByTypeIterator::ACCEPT_LINK,
                $this->createTypes(false, false, true),
            ],
            'everything' => [
                FilesystemFilterByTypeIterator::ACCEPT_ALL,
                $this->createTypes(true, true, true),
            ],
        ];
    }

    /**
     * @covers ::accept
     * @depends testConstruct
     * @dataProvider provideTestAcceptData
     *
     * @param array                          $expect
     * @param int                            $flags
     * @param FilesystemFilterByTypeIterator $iterator
     */
    public function testAccept(array $expect, int $flags, FilesystemFilterByTypeIterator $iterator)
    {
        $this->getFilesystem();
        $iterator->setFlags($flags);
        $actual = array_keys(iterator_to_array($iterator, true));
        sort($actual);
        $this->assertSame($expect, $actual);
    }

    /**
     * @return array
     */
    public function provideTestAcceptData(): array
    {
        return [
            'nothing' => [
                [],
                FilesystemFilterByTypeIterator::ACCEPT_NONE,
            ],
            'files' => [
                [
                    'vfs://root-777/file',
                ],
                FilesystemFilterByTypeIterator::ACCEPT_FILE,
            ],
            'directories' => [
                [
                    'vfs://root-777/folder',
                ],
                FilesystemFilterByTypeIterator::ACCEPT_DIRECTORY,
            ],
            'symbolic links' => [
                [
                    'vfs://root-777/dangling-symlink',
                    'vfs://root-777/symlink',
                ],
                FilesystemFilterByTypeIterator::ACCEPT_LINK,
            ],
            'everything' => [
                [
                    'vfs://root-777/dangling-symlink',
                    'vfs://root-777/file',
                    'vfs://root-777/folder',
                    'vfs://root-777/symlink',
                ],
                FilesystemFilterByTypeIterator::ACCEPT_ALL,
            ],
        ];
    }

    /**
     * @covers ::createGetFileTypeClosure
     * @depends testConstruct
     *
     * @param FilesystemFilterByTypeIterator $iterator
     */
    public function testCreateGetFileTypeClosure(FilesystemFilterByTypeIterator $iterator)
    {
        $fs = $this->getFilesystem();

        $closure = $this->callProtectedMethod(
            $iterator,
            'createGetFileTypeClosure',
            \FilesystemIterator::CURRENT_AS_PATHNAME
        );
        $this->assertSame('file', $closure($fs->getChild('file')->url()));

        $closure = $this->callProtectedMethod(
            $iterator,
            'createGetFileTypeClosure',
            \FilesystemIterator::CURRENT_AS_FILEINFO
        );
        $this->assertSame('file', $closure(new \SplFileInfo($fs->getChild('file')->url())));

        $closure = $this->callProtectedMethod(
            $iterator,
            'createGetFileTypeClosure',
            \FilesystemIterator::CURRENT_AS_SELF
        );

        /** @var VfsFilesystemIterator $innerIterator */
        $innerIterator = $iterator->getInnerIterator();
        $innerIterator->rewind();
        $this->assertSame($innerIterator->getType(), $closure($innerIterator));
    }

    /**
     * @covers ::createGetFileTypeClosure
     * @depends testConstruct
     *
     * @param FilesystemFilterByTypeIterator $iterator
     */
    public function testCreateGetFileTypeClosureWithInvalidFlagsThrowsException(FilesystemFilterByTypeIterator $iterator)
    {
        $flag = -1;
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Failed to decode iterator flags: %s (%b)', $flag, $flag));
        $this->expectExceptionCode(1522919722);

        $this->callProtectedMethod($iterator, 'createGetFileTypeClosure', $flag);
    }

    // ////////////////////////////////////////////////////////////////
    // utility methods
    // ////////////////////////////////////////////////////////////////

    /**
     * @param bool $file
     * @param bool $dir
     * @param bool $link
     *
     * @return bool[]
     */
    protected function createTypes(bool $file, bool $dir, bool $link): array
    {
        return ['file' => $file, 'dir' => $dir, 'link' => $link];
    }
}
