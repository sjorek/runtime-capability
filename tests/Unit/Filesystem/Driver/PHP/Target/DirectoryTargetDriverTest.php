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

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * DirectoryTargetDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver
 */
class DirectoryTargetDriverTest extends AbstractFilesystemTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DirectoryTargetDriver();
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
     * @covers ::createDirectory
     */
    public function testCreate()
    {
        $fs = $this->getFilesystem();

        // touch new file
        $path = $fs->url() . '/' . 'test';

        $this->assertFalse($fs->hasChild('test'));
        $this->assertDirectoryNotExists($path);

        $this->assertTrue($this->subject->createTarget($path));

        $this->assertTrue($fs->hasChild('test'));
        $this->assertDirectoryExists($path);
    }

    /**
     * @covers ::createTarget
     * @covers ::createDirectory
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
     * @covers ::directoryExists
     */
    public function testExistsForNonExistingPath()
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . 'non-existent';
        $this->assertFalse($this->subject->targetExists($path));
        $this->assertDirectoryNotExists($path);
    }

    /**
     * @covers ::targetExists
     * @covers ::directoryExists
     * @dataProvider provideTestExistsForExistingPathsData
     */
    public function testExistsForExistingPaths(bool $expect, string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        if ($expect) {
            $this->assertTrue($this->subject->targetExists($path));
            $this->assertDirectoryExists($path);
        } else {
            $this->assertFalse($this->subject->targetExists($path));
            $this->assertFileExists($path);
        }
    }

    /**
     * @return string[][]
     */
    public function provideTestExistsForExistingPathsData()
    {
        return array_map(
            function (array $value) {
                return ['folder' === $value[0], $value[0]];
            },
            $this->provideTestCreateOnExistingPathsThrowsExceptionData()
        );
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeDirectory
     */
    public function testRemove()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/folder';
        $this->assertDirectoryExists($path);
        $this->assertTrue($this->subject->removeTarget($path));
        $this->assertDirectoryNotExists($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeDirectory
     */
    public function testRemoveWithNonExistentPathThrowsException()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/not-existent';
        $this->assertFileNotExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The directory does not exist: %s.', $path));
        $this->expectExceptionCode(1522315070);

        $this->subject->removeTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeDirectory
     * @dataProvider provideTestRemoveWithNoneDirectoryThrowsExceptionData
     */
    public function testRemoveWithNoneDirectoryThrowsException(string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        $this->assertFileExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The given path is not a directory: %s.', $path));
        $this->expectExceptionCode(1522315073);

        $this->subject->removeTarget($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestRemoveWithNoneDirectoryThrowsExceptionData()
    {
        return array_filter(
            $this->provideTestCreateOnExistingPathsThrowsExceptionData(),
            function ($value) {
                return 'folder' !== $value[0];
            }
        );
    }

    /**
     * @covers ::createTarget
     * @covers ::createDirectory
     */
    public function testCreateOnReadOnlyFilesystemThrowsException()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);

        // touch new file
        $path = $fs->url() . '/' . 'test';
        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp(
            sprintf(
                '/^Failed to create directory %s: /u',
                preg_quote($path, '/')
            )
        );
        $this->expectExceptionCode(1522314976);

        $this->subject->createTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeDirectory
     */
    public function testRemoveOnReadOnlyFilesystemThrowsException()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $path = $fs->url() . '/folder';

        $this->assertTrue($fs->hasChild('folder'));
        $this->assertDirectoryExists($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Failed to remove directory: %s',
                $path
            )
        );
        $this->expectExceptionCode(1522315076);

        $this->subject->removeTarget($path);
    }
}
