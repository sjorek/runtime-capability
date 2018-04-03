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

namespace Sjorek\RuntimeCapability\Tests\Unit\Utility;

use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;

/**
 * FilesystemUtility test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Utility\FilesystemUtility
 */
class FilesystemUtilityTest extends AbstractFilesystemTestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        unset($GLOBALS[$this->getUtilityNamespace()]);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($GLOBALS[$this->getUtilityNamespace()]);

        parent::tearDown();
    }

    /**
     * @covers ::pathExists
     * @testWith [true, "file"]
     *           [true, "folder"]
     *           [true, "symlink"]
     *           [true, "dangling-symlink"]
     *           [false, "non-existent"]
     */
    public function testPathExists(bool $expect, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::pathExists($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isFile
     * @testWith [true, "file"]
     *           [false, "folder"]
     *           [false, "symlink"]
     *           [false, "dangling-symlink"]
     *           [false, "non-existent"]
     */
    public function testIsFile(bool $expect, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::isFile($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isSymbolicLink
     * @testWith [false, "file"]
     *           [false, "folder"]
     *           [true, "symlink"]
     *           [true, "dangling-symlink"]
     *           [false, "non-existent"]
     */
    public function testIsSymbolicLink(bool $expect, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::isSymbolicLink($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isDirectory
     * @testWith [false, "file"]
     *           [true, "folder"]
     *           [false, "symlink"]
     *           [false, "dangling-symlink"]
     *           [false, "non-existent"]
     */
    public function testIsDirectory(bool $expect, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::isDirectory($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isAccessibleDirectory
     * @testWith [true, "0777", "folder"]
     *           [true, "0766", "folder"]
     *           [true, "0676", "folder"]
     *           [true, "0667", "folder"]
     *           [false, "0666", "folder"]
     *           [false, "0777", "file"]
     *           [false, "0777", "non-existent"]
     */
    public function testIsAccessibleDirectory(bool $expect, string $mode, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->chmod(octdec($mode))->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::isAccessibleDirectory($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isWritableDirectory
     * @testWith [true, "0777", "folder"]
     *           [true, "0755", "folder"]
     *           [true, "0575", "folder"]
     *           [true, "0557", "folder"]
     *           [false, "0555", "folder"]
     *           [false, "0777", "file"]
     *           [false, "0777", "non-existent"]
     */
    public function testIsWritableDirectory(bool $expect, string $mode, string $path)
    {
        $fs = $this->getFilesystem();
        if ($fs->hasChild($path)) {
            $path = $fs->getChild($path)->chmod(octdec($mode))->url();
        } else {
            $path = $fs->url() . '/' . $path;
        }
        $actual = FilesystemUtility::isWritableDirectory($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::normalizePath
     * @dataProvider provideTestNormalizePathData
     */
    public function testNormalizePath(string $expect, string $input)
    {
        $this->assertSame($expect, FilesystemUtility::normalizePath($input));
    }

    /**
     * @return string[][]
     */
    public function provideTestNormalizePathData()
    {
        return array_map(
            function ($value) {
                $value[0] = preg_replace('/^EMPTY/', '', $value[0]);

                return $value;
            },
            $this->extractTestDataFromDocComment(
               (new \ReflectionMethod(FilesystemUtility::class, 'normalizePath'))->getDocComment()
            )
        );
    }

    /**
     * @covers ::getCurrentWorkingDirectory
     */
    public function testGetCurrentWorkingDirectory()
    {
        $ns = $this->getUtilityNamespace();

        $GLOBALS[$ns]['getcwd'] = false;
        $GLOBALS[$ns]['realpath']['.'] = false;
        $this->assertSame('.', FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['getcwd']);
        unset($GLOBALS[$ns]['realpath']);

        $GLOBALS[$ns]['getcwd'] = '/';
        $this->assertSame('/', FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['getcwd']);

        $GLOBALS[$ns]['getcwd'] = '\\';
        $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        $this->assertSame('/', FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['getcwd']);
        unset($GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR']);

        $GLOBALS[$ns]['getcwd'] = 'c:\\';
        $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        $this->assertSame('c:/', FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['getcwd']);
        unset($GLOBALS[$ns]['constant']);

        $GLOBALS[$ns]['getcwd'] = false;
        $this->assertSame(strtr(getcwd(), '\\', '/'), FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['getcwd']);

        $GLOBALS[$ns]['realpath']['.'] = false;
        $this->assertSame(strtr(realpath('.'), '\\', '/'), FilesystemUtility::getCurrentWorkingDirectory());
        unset($GLOBALS[$ns]['realpath']);
    }

    /**
     * @covers ::isAbsolutePath
     */
    public function testIsAbsolutePath()
    {
        $ns = $this->getUtilityNamespace();

        $this->assertFalse(FilesystemUtility::isAbsolutePath(''));
        $this->assertFalse(FilesystemUtility::isAbsolutePath('.'));
        $this->assertTrue(FilesystemUtility::isAbsolutePath('/'));

        $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        $this->assertFalse(FilesystemUtility::isAbsolutePath('c:'));
        $this->assertTrue(FilesystemUtility::isAbsolutePath('c:/'));
        $this->assertTrue(FilesystemUtility::isAbsolutePath('c:\\'));
        $this->assertTrue(FilesystemUtility::isAbsolutePath('\\'));
        unset($GLOBALS[$ns]['constant']);
    }

    /**
     * @covers ::isUrl
     * @testWith [false, ""]
     *           [false, "file"]
     *           [true, "file:"]
     *           [true, "file:."]
     *           [true, "file:.."]
     *           [true, "file:test.txt"]
     *           [true, "file:/"]
     *           [true, "file:/test.txt"]
     *           [true, "file://"]
     *           [true, "file:///"]
     *           [true, "file:///test.txt"]
     *           [true, "file://localhost"]
     *           [true, "file://localhost/"]
     *           [true, "file://localhost/test.txt"]
     */
    public function testIsUrl(bool $expect, string $path)
    {
        $actual = FilesystemUtility::isUrl($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::isLocalPath
     * @testWith [false, ""]
     *           [true, "non-existent"]
     *           [true, "."]
     *           [true, ".."]
     *           [false, "file:"]
     *           [true, "file:."]
     *           [true, "file:.."]
     *           [true, "file:non-existent"]
     *           [true, "file:/"]
     *           [true, "file:/non-existent"]
     *           [false, "file://"]
     *           [true, "file:///"]
     *           [true, "file:///non-existent"]
     *           [false, "file://localhost"]
     *           [true, "file://localhost/"]
     *           [true, "file://localhost/non-existent"]
     *           [false, "file://non-localhost"]
     *           [true, "file://non-localhost/"]
     *           [true, "file://non-localhost/non-existent"]
     *           [false, "vfs://root-777"]
     *           [true, "vfs://root-777/"]
     *           [true, "vfs://root-777/file"]
     *           [true, "vfs://root-777/folder"]
     *           [true, "vfs://root-777/symlink"]
     *           [true, "vfs://root-777/dangling-symlink"]
     *           [true, "vfs://root-777/non-existent"]
     */
    public function testIsLocalPath(bool $expect, string $path)
    {
        $this->getFilesystem();
        $actual = FilesystemUtility::isLocalPath($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    /**
     * @covers ::hasWindowsDrivePrefix
     * @testWith [false, ""]
     *           [false, "file"]
     *           [false, "c"]
     *           [false, "@:"]
     *           [false, "[:"]
     *           [false, "`:"]
     *           [false, "{:"]
     *           [true, "c:"]
     *           [true, "c:/"]
     *           [true, "c:\\"]
     *           [true, "z:"]
     *           [true, "z:/"]
     *           [true, "z:\\"]
     *           [true, "C:"]
     *           [true, "C:/"]
     *           [true, "C:\\"]
     *           [true, "Z:"]
     *           [true, "Z:/"]
     *           [true, "Z:\\"]
     */
    public function testHasWindowsDrivePrefix(bool $expect, string $path)
    {
        $actual = FilesystemUtility::hasWindowsDrivePrefix($path);
        if ($expect) {
            $this->assertTrue($actual);
        } else {
            $this->assertFalse($actual);
        }
    }

    // ////////////////////////////////////////////////////////////////
    // utility methods
    // ////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    protected function getUtilityNamespace(): string
    {
        return implode('\\', array_slice(explode('\\', FilesystemUtility::class), 0, -1));
    }
}
