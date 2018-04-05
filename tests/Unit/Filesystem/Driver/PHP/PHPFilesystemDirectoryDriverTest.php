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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FileTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
// use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver;
use Sjorek\RuntimeCapability\Iteration\FilesystemFilterByTypeIterator;
use Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\PHP\PHPFilesystemDriverTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemDirectoryDriver
 */
class PHPFilesystemDirectoryDriverTest extends AbstractFilesystemTestCase
{
    /**
     * @covers ::__construct
     *
     * @return PHPFilesystemDirectoryDriver
     */
    public function testConstruct(): PHPFilesystemDirectoryDriver
    {
        // test default behavior
        $driver = new PHPFilesystemDirectoryDriver();
        $this->assertAttributeInstanceOf(FileTargetDriverInterface::class, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(FileTargetDriver::class, 'targetDriver', $driver);

        // test custom behavior
        $targetDriver = new PHPFilesystemDriverTestFixture();
        $driver = new PHPFilesystemDirectoryDriver($targetDriver);

        $this->assertInstanceOf(FilesystemDirectoryDriverInterface::class, $driver);
        $this->assertAttributeSame($targetDriver, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(FilesystemDriverInterface::class, 'targetDriver', $driver);

        return $driver;
    }

    /**
     * @covers ::__construct
     * @depends testConstruct
     *
     * @return PHPFilesystemDirectoryDriver
     */
    public function testConstructWithInvalidDriverThrowsExcpetion(PHPFilesystemDirectoryDriver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                    'The driver must not implement the interface: %s',
                    FilesystemDirectoryDriverInterface::class
            )
        );
        $this->expectExceptionCode(1522331750);

        new PHPFilesystemDirectoryDriver($driver);
    }

    /**
     * @covers ::setDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemDirectoryDriver $driver
     *
     * @return PHPFilesystemDirectoryDriver
     */
    public function testSetDirectory(PHPFilesystemDirectoryDriver $driver): PHPFilesystemDirectoryDriver
    {
        $fs = $this->getFilesystem();
        $directory = $fs->url();

        $this->assertSame($directory, $driver->setDirectory($directory));
        $this->assertAttributeSame($directory, 'workingDirectory', $driver);

        return $driver;
    }

    /**
     * @covers ::setDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testSetDirectoryWithNonDirectoryThrowsException(PHPFilesystemDirectoryDriver $driver)
    {
        $fs = $this->getFilesystem();
        $path = $fs->getChild('file')->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid path given: %s. The directory does not exist.',
                $path
            )
        );
        $this->expectExceptionCode(1522171543);

        $driver->setDirectory($path);
    }

    /**
     * @covers ::setDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testSetDirectoryWithNonWriteableDirectoryThrowsException(PHPFilesystemDirectoryDriver $driver)
    {
        $fs = $this->getFilesystem();
        $path = $fs->getChild('folder')->chmod(self::FILESYSTEM_MODE_READ_ONLY)->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid path given: %s. The directory is not writable.',
                $path
            )
        );
        $this->expectExceptionCode(1522171546);

        $driver->setDirectory($path);
    }

    /**
     * @covers ::setDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testSetDirectoryWithNonExecutableDirectoryThrowsException(PHPFilesystemDirectoryDriver $driver)
    {
        $fs = $this->getFilesystem();
        $path = $fs->getChild('folder')->chmod(self::FILESYSTEM_MODE_NOT_EXECUTEABLE)->url();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid path given: %s. The directory accessible (executable).',
                $path
            )
        );
        $this->expectExceptionCode(1522171549);

        $driver->setDirectory($path);
    }

    /**
     * @covers ::getDirectory
     * @depends testSetDirectory
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testGetDirectory(PHPFilesystemDirectoryDriver $driver)
    {
        $this->assertSame($this->getFilesystem()->url(), $driver->getDirectory());
    }

    /**
     * @covers ::createTarget
     * @depends testSetDirectory
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testCreateTarget(PHPFilesystemDirectoryDriver $driver)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/test';

        $this->assertFileNotExists($path);
        $this->assertFalse($fs->hasChild(basename($path)));

        $this->assertTrue($driver->createTarget(basename($path)));

        $this->assertFileExists($path);
        $this->assertTrue($fs->hasChild(basename($path)));
    }

    /**
     * @covers ::targetExists
     * @depends testSetDirectory
     * @dataProvider provideTestTargetExistsData
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testTargetExists(bool $expect, string $file, PHPFilesystemDirectoryDriver $driver)
    {
        $this->getFilesystem();
        $actual = $driver->targetExists($file);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * return array.
     */
    public function provideTestTargetExistsData()
    {
        $data = array_combine(
            array_keys(self::FILESYSTEM_STRUCTURE),
            array_map(
                function ($file) {
                    return [true, $file];
                },
                array_keys(self::FILESYSTEM_STRUCTURE)
            )
        );
        $data['non-existent'] = [false, 'non-existent'];

        return $data;
    }

    /**
     * @covers ::removeTarget
     * @depends testSetDirectory
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testRemoveTarget(PHPFilesystemDirectoryDriver $driver)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/file';

        $this->assertFileExists($path);
        $this->assertTrue($fs->hasChild(basename($path)));

        $this->assertTrue($driver->removeTarget(basename($path)));

        $this->assertFileNotExists($path);
        $this->assertFalse($fs->hasChild(basename($path)));
    }

    /**
     * @covers ::setIteratorPattern
     * @depends testSetDirectory
     *
     * @param PHPFilesystemDirectoryDriver $driver
     *
     * @return PHPFilesystemDirectoryDriver
     */
    public function testSetIteratorPattern(PHPFilesystemDirectoryDriver $driver): PHPFilesystemDirectoryDriver
    {
        $driver->setIteratorPattern('*');
        $this->assertAttributeSame('*', 'iteratorPattern', $driver);

        return $driver;
    }

    /**
     * @covers ::getIterator
     * @depends testSetIteratorPattern
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testGetIterator(PHPFilesystemDirectoryDriver $driver)
    {
        $this->assertInstanceOf(\Traversable::class, $driver);
        $this->assertInstanceOf(\IteratorAggregate::class, $driver);

        $path = $this->getFilesystem()->url() . '/';

        $actual = array_map(
            function (\SplFileInfo $fileInfo): string {
                return $fileInfo->getPathname();
            },
            iterator_to_array($driver, true)
        );

        $expect = array_combine(
            array_keys(self::FILESYSTEM_STRUCTURE),
            array_map(
                function (string $file) use ($path): string {
                    return $path . $file;
                },
                array_keys(self::FILESYSTEM_STRUCTURE)
            )
        );

        ksort($actual);
        ksort($expect);

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::prependDirectory
     * @depends testSetDirectory
     *
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testPrependDirectory(PHPFilesystemDirectoryDriver $driver)
    {
        $path = $this->getFilesystem()->url() . '/test';

        $this->assertSame($path, $this->callProtectedMethod($driver, 'prependDirectory', 'test'));
        $this->assertSame($path, $this->callProtectedMethod($driver, 'prependDirectory', './test'));
    }

    /**
     * @covers ::prependDirectory
     * @depends testSetDirectory
     * @testWith ["/"]
     *           ["/test"]
     *           ["c:/"]
     *           ["c:\\"]
     *
     * @param string                       $path
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testPrependDirectoryToAbsolutePathThrowsException(string $path, PHPFilesystemDirectoryDriver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Invalid path given: %s. Can not prepend directory to an absolute path.', $path)
        );
        $this->expectExceptionCode(1522171647);

        if (0 === strpos($path, 'c:')) {
            $ns = $this->getFilesystemUtilityNamespace();
            $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        }
        $this->callProtectedMethod($driver, 'prependDirectory', $path);
    }

    /**
     * @covers ::prependDirectory
     * @depends testSetDirectory
     * @testWith ["file://localhost/"]
     *           ["file:///"]
     *           ["vfs://root-777/"]
     *
     * @param string                       $url
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testPrependDirectoryToLocalUrlThrowsException(
        string $url,
        PHPFilesystemDirectoryDriver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Invalid path given: %s. Can not prepend directory to an url.', $url)
        );
        $this->expectExceptionCode(1522171650);

        $this->callProtectedMethod($driver, 'prependDirectory', $url);
    }

    /**
     * @covers ::prependDirectory
     * @depends testSetDirectory
     * @testWith ["."]
     *           ["./"]
     *           [".\\"]
     *
     * @param string                       $path
     * @param PHPFilesystemDirectoryDriver $driver
     */
    public function testPrependDirectoryToDotDirectoryThrowsException(
        string $path,
        PHPFilesystemDirectoryDriver $driver)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid path given. Can not prepend directory to current directory (.) without any path.'
        );
        $this->expectExceptionCode(1522318072);

        $this->callProtectedMethod($driver, 'prependDirectory', $path);
    }

    /**
     * @covers ::createFilesystemIterator
     * @dataProvider provideTestCreateFilesystemIteratorData
     *
     * @param PHPFilesystemDirectoryDriver $driver
     * @param string                       $innerIteratorClass
     * @param string                       $innerIteratorClass
     * @param array                        $structure
     */
    public function testCreateFilesystemIterator(
        PHPFilesystemDirectoryDriver $driver,
        string $innerIteratorClass,
        string $pattern,
        array $structure)
    {
        $path = $this->getFilesystem()->url();

        /** @var GlobFilterKeyIterator $iterator */
        $iterator = $this->callProtectedMethod($driver, 'createFilesystemIterator', $path, $pattern);

        $this->assertAttributeSame($pattern, 'pattern', $iterator);
        $this->assertInstanceOf($innerIteratorClass, $iterator->getInnerIterator());
        $this->assertSame($structure, array_keys(iterator_to_array($iterator, true)));
    }

    /**
     * @return array
     */
    public function provideTestCreateFilesystemIteratorData(): array
    {
        return [
            'file target' => [
                new PHPFilesystemDirectoryDriver(new FileTargetDriver()),
                FilesystemFilterByTypeIterator::class,
                'vfs://root-777/f*',
                ['vfs://root-777/file'],
            ],
            'directory target' => [
                new PHPFilesystemDirectoryDriver(new DirectoryTargetDriver()),
                FilesystemFilterByTypeIterator::class,
                'vfs://root-777/f*',
                ['vfs://root-777/folder'],
            ],
            // symbolic links are not yet supported with php + vfs-stream-wrappers
            // @see https://github.com/mikey179/vfsStream/issues/89
            // @see https://wiki.php.net/rfc/linking_in_stream_wrappers
            // 'link target' => [
            //     new PHPFilesystemDirectoryDriver(new LinkTargetDriver()),
            //     FilesystemFilterByTypeIterator::class,
            //     'vfs://root-777/s*',
            //     ['vfs://root-777/symlink'],
            // ],
            'any target' => [
                new PHPFilesystemDirectoryDriver(new PHPFilesystemDriverTestFixture()),
                \FilesystemIterator::class,
                'vfs://root-777/f*',
                ['vfs://root-777/file', 'vfs://root-777/folder'],
            ],
        ];
    }
}
