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
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemDirectoryDriver;
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
        $path = $this->getFilesystem()->url() . '/';
        $this->assertInstanceOf(\Traversable::class, $driver);
        $this->assertInstanceOf(\IteratorAggregate::class, $driver);

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
}
