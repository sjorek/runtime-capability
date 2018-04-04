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

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * LinkTargetDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver
 */
class LinkTargetDriverTest extends AbstractFilesystemTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        require_once str_replace(
            ['/Unit/', '.php'],
            ['/Fixtures/', 'Fixture.php'],
            __FILE__
        );

        $this->subject = new LinkTargetDriver();
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
     * @covers ::createSymbolicLink
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
    }

    /**
     * @covers ::createTarget
     * @covers ::createSymbolicLink
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
     * @covers ::symbolicLinkExists
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
     * @covers ::symbolicLinkExists
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
                return [false !== strpos($value[0], 'symlink'), $value[0]];
            },
            $this->provideTestCreateOnExistingPathsThrowsExceptionData()
        );
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeSymbolicLink
     */
    public function testRemove()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/symlink';
        $this->assertFileExists($path);
        $this->assertTrue($this->subject->removeTarget($path));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeSymbolicLink
     */
    public function testRemoveWithNonExistentPathThrowsException()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/not-existent';
        $this->assertFileNotExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The symlink does not exist: %s.', $path));
        $this->expectExceptionCode(1522314870);

        $this->subject->removeTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeSymbolicLink
     * @dataProvider provideTestRemoveWithNoneLinkThrowsExceptionData
     */
    public function testRemoveWithNoneLinkThrowsException(string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        $this->assertFileExists($path);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The given path is not a symlink: %s.', $path));
        $this->expectExceptionCode(1522314873);

        $this->subject->removeTarget($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestRemoveWithNoneLinkThrowsExceptionData()
    {
        return array_filter(
            $this->provideTestCreateOnExistingPathsThrowsExceptionData(),
            function ($value) {
                return false === strpos($value[0], 'symlink');
            }
        );
    }

    /**
     * @covers ::createTarget
     * @covers ::createSymbolicLink
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
                '/^Failed to create symlink %s: /u',
                preg_quote($path, '/')
            )
        );
        $this->expectExceptionCode(1522314776);

        $this->subject->createTarget($path);
    }

    /**
     * @covers ::removeTarget
     * @covers ::removeSymbolicLink
     */
    public function testRemoveOnReadOnlyFilesystemThrowsException()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $path = $fs->url() . '/symlink';

        $this->assertTrue($fs->hasChild('symlink'));
        $this->assertFileExists($path);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Failed to remove symlink: %s',
                $path
            )
        );
        $this->expectExceptionCode(1522314876);

        $this->subject->removeTarget($path);
    }
}
