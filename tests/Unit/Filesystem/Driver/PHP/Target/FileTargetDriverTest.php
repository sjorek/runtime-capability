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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver\PHP\Target;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * FileTargetDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver
 */
class FileTargetDriverTest extends AbstractFilesystemTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new FileTargetDriver();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        parent::tearDown();
    }

    /**
     * @covers ::createTarget
     * @covers ::createFile
     */
    public function testCreate()
    {
        $fs = $this->getFilesystem();

        // touch new file
        $path = $fs->url() . '/' . 'test';

        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);

        $this->assertTrue($this->subject->createTarget($path));

        $this->assertTrue($fs->hasChild('test'));
        $this->assertFileExists($path);
        $this->assertFileIsReadable($path);
        $this->assertFileIsWritable($path);
        $this->assertTrue(is_file($path));
    }

    /**
     * @covers ::createTarget
     * @covers ::createFile
     * @dataProvider provideTestCreateOnExistingPathsThrowsExceptionData
     */
    public function testCreateOnExistingPathsThrowsException(string $file)
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/' . $file;

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The path already exists: %s.', $path));
        $this->expectExceptionCode(1522314570);

        $this->subject->createTarget($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestCreateOnExistingPathsThrowsExceptionData()
    {
        return array_combine(
            array_keys(self::FILESYSTEM_STRUCTURE),
            array_map(
                function ($file) {
                    return [$file];
                },
                array_keys(self::FILESYSTEM_STRUCTURE)
            )
        );
    }

    /**
     * @covers ::targetExists
     * @covers ::fileExists
     */
    public function testExistsForNonExistingPath()
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . 'non-existent';
        $this->assertFalse($this->subject->targetExists($path));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::targetExists
     * @covers ::fileExists
     * @dataProvider provideTestExistsForExistingPathsData
     */
    public function testExistsForExistingPaths(bool $expect, string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        if ($expect) {
            $this->assertTrue($this->subject->targetExists($path));
        } else {
            $this->assertFalse($this->subject->targetExists($path));
        }
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestExistsForExistingPathsData()
    {
        return array_map(
            function (array $value) {
                return ['file' === $value[0], $value[0]];
            },
            $this->provideTestCreateOnExistingPathsThrowsExceptionData()
        );
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeFile
     */
    public function testRemove()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/file';
        $this->assertFileExists($path);
        $this->assertTrue($this->subject->removeTarget($path));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeFile
     */
    public function testRemoveWithNonExistentPathThrowsException()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/not-existent';
        $this->assertFileNotExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The file does not exist: %s.', $path));
        $this->expectExceptionCode(1522314670);

        $this->subject->removeTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeFile
     * @dataProvider provideTestRemoveWithNoneFileThrowsExceptionData
     */
    public function testRemoveWithNoneFileThrowsException(string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        $this->assertFileExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The given path is not a file: %s.', $path));
        $this->expectExceptionCode(1522314673);

        $this->subject->removeTarget($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestRemoveWithNoneFileThrowsExceptionData()
    {
        return array_filter(
            $this->provideTestCreateOnExistingPathsThrowsExceptionData(),
            function ($value) {
                return 'file' !== $value[0];
            }
        );
    }

    /**
     * @covers ::createTarget
     * @covers ::createFile
     */
    public function testCreateOnReadOnlyFilesystemThrowsException()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);

        // touch new file
        $path = $fs->url() . '/' . 'test';
        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Failed to create file %s: Can not create new file in non-writable path %s',
                $path,
                basename($fs->url())
            )
        );
        $this->expectExceptionCode(1522314576);

        $this->subject->createTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeFile
     */
    public function testRemoveOnReadOnlyFilesystemThrowsException()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $path = $fs->url() . '/file';

        $this->assertTrue($fs->hasChild('file'));
        $this->assertFileExists($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Failed to remove file: %s',
                $path
            )
        );
        $this->expectExceptionCode(1522314676);

        $this->subject->removeTarget($path);
    }
}
