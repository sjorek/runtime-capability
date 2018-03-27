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

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver
 */
class FilesystemDriverTest extends AbstractTestCase
{
    /**
     * @var string[]
     */
    const FILESYSTEM_STRUCTURE = [
        'file' => 'file',
        'folder' => [],
        'symlink' => 'symlink',
        'dangling-symlink' => 'dangling-symlink'
    ];

    /**
     * @var integer
     */
    const FILESYSTEM_MODE_FULL_ACCESS = 0777;

    /**
     * @var integer
     */
    const FILESYSTEM_MODE_READ_ONLY = 0555;

    /**
     * @var integer
     */
    const FILESYSTEM_MODE_NOT_EXECUTEABLE = 0666;

    /**
     * @var FilesystemDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        require_once str_replace(['/Unit/', '.php'], ['/Fixtures/', 'Fixture.php'], __FILE__);

        $this->subject = new FilesystemDriver();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;
        vfsStreamWrapper::unregister();

        parent::tearDown();
    }

    /**
     * @covers ::create
     * @covers ::exists
     */
    public function testCreate()
    {
        $fs = $this->getFilesystem();

        // touch new file
        $path = $fs->url() . '/' . 'test';

        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);

        $this->assertTrue($this->subject->create($path));

        $this->assertTrue($fs->hasChild('test'));
        $this->assertFileExists($path);
        $this->assertFileIsReadable($path);
        $this->assertFileIsWritable($path);
        $this->assertTrue(is_file($path));
    }
    /**
     * @covers ::create
     * @covers ::exists
     * @dataProvider provideTestCreateOnExistingPathsDeniesCreationData
     */
    public function testCreateOnExistingPathsDeniesCreation(string $file)
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/' . $file;

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);

        $this->assertFalse($this->subject->create($path));

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestCreateOnExistingPathsDeniesCreationData()
    {
        return array_combine(
            array_keys(self::FILESYSTEM_STRUCTURE),
            array_map(
                function($file) {
                    return [$file];
                },
                array_keys(self::FILESYSTEM_STRUCTURE)
            )
        );
    }

    /**
     * @covers ::exists
     */
    public function testExistsForNonExistingPath()
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . 'non-existent';
        $this->assertFalse($this->subject->exists($path));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::exists
     * @dataProvider provideTestExistsForExistingPathsData
     */
    public function testExistsForExistingPaths(string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        $this->assertTrue($this->subject->exists($path));
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestExistsForExistingPathsData()
    {
        return $this->provideTestCreateOnExistingPathsDeniesCreationData();
    }

    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $fs = $this->getFilesystem();

        $path = $fs->url() . '/file';
        $this->assertFileExists($path);
        $this->assertTrue($this->subject->remove($path));
        $this->assertFileNotExists($path);

        $path = $fs->url() . '/not-existent';
        $this->assertFileNotExists($path);
        $this->assertFalse($this->subject->remove($path));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::remove
     * @dataProvider provideTestRemoveWithNoneFileDeniesRemovalData
     */
    public function testRemoveWithNoneFileDeniesRemoval(string $file)
    {
        $fs = $this->getFilesystem();
        $path = $fs->url() . '/' . $file;
        $this->assertFileExists($path);
        $this->assertFalse($this->subject->remove($path));
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestRemoveWithNoneFileDeniesRemovalData()
    {
        return array_filter(
            $this->provideTestCreateOnExistingPathsDeniesCreationData(),
            function($value) {
                return 'file' !== $value[0];
            }
        );
    }

    /**
     * @covers ::create
     */
    public function testCreateOnReadOnlyFilesystemDeniesCreation()
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);

        // touch new file
        $path = $fs->url() . '/' . 'test';
        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);

        $this->assertFalse($this->subject->create($path));

        $this->assertFalse($fs->hasChild('test'));
        $this->assertFileNotExists($path);
    }

    /**
     * @covers ::create
     * @dataProvider provideTestCreateOnReadOnlyFilesystemWithNoneFileDeniesCreationData
     */
    public function testCreateOnReadOnlyFilesystemWithNoneFileDeniesCreation(string $file)
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $path = $fs->url() . '/' . $file;

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);

        $this->assertFalse($this->subject->create($path));

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestCreateOnReadOnlyFilesystemWithNoneFileDeniesCreationData()
    {
        return $this->provideTestRemoveWithNoneFileDeniesRemovalData();
    }

    /**
     * @covers ::remove
     * @dataProvider provideTestRemoveOnReadOnlyFilesystemDeniesRemovalData
     */
    public function testRemoveOnReadOnlyFilesystemDeniesRemoval(string $file)
    {
        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $path = $fs->url() . '/' . $file;

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);

        $this->assertFalse($this->subject->remove($path));

        $this->assertTrue($fs->hasChild($file));
        $this->assertFileExists($path);
    }

    /**
     * @return string[][]
     */
    public function provideTestRemoveOnReadOnlyFilesystemDeniesRemovalData()
    {
        return $this->provideTestCreateOnExistingPathsDeniesCreationData();
    }

    /**
     * @covers ::normalizePath
     * @dataProvider provideTestNormalizePathData
     */
    public function testNormalizePath(string $expect, string $input)
    {
        $this->assertSame($expect, $this->callProtectedMethod($this->subject, 'normalizePath', $input));
    }

    /**
     * @return string[][]
     */
    public function provideTestNormalizePathData()
    {
        $cwd = getcwd() ?: '.';

        return array_map(
            function ($value) use($cwd) {
                $value[0] = preg_replace('/^CWD/', $cwd, $value[0]);

                return $value;
            },
            $this->extractTestDataFromDocComment(
               (new \ReflectionMethod(FilesystemDriver::class, 'normalizePath'))->getDocComment()
            )
        );
    }

    /**
     * @covers ::hasWritableParentDirectory
     */
    public function testHasWritableParentDirectory()
    {
        $fs = $this->getFilesystem();
        $this->assertTrue(
            $this->callProtectedMethod($this->subject, 'hasWritableParentDirectory', $fs->url() . '/file')
        );

        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $this->assertFalse(
            $this->callProtectedMethod($this->subject, 'hasWritableParentDirectory', $fs->url() . '/file')
        );

        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_NOT_EXECUTEABLE);
        $this->assertTrue(
            $this->callProtectedMethod($this->subject, 'hasWritableParentDirectory', $fs->url() . '/file')
        );
    }

    /**
     * @covers ::hasExecutableParentDirectory
     */
    public function testHasExecutableParentDirectory()
    {
        $fs = $this->getFilesystem();
        $this->assertTrue(
            $this->callProtectedMethod($this->subject, 'hasExecutableParentDirectory', $fs->url() . '/file')
        );

        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_READ_ONLY);
        $this->assertTrue(
            $this->callProtectedMethod($this->subject, 'hasExecutableParentDirectory', $fs->url() . '/file')
        );

        $fs = $this->getFilesystem(self::FILESYSTEM_MODE_NOT_EXECUTEABLE);
        $this->assertFalse(
            $this->callProtectedMethod($this->subject, 'hasExecutableParentDirectory', $fs->url() . '/file')
        );
    }

    // ////////////////////////////////////////////////////////////////
    // utility methods
    // ////////////////////////////////////////////////////////////////

    /**
     * @param boolean $readOnly
     * @return vfsStreamDirectory
     */
    protected function getFilesystem(int $mode = self::FILESYSTEM_MODE_FULL_ACCESS): vfsStreamDirectory
    {
        return vfsStream::setup(
            sprintf('root-%o', $mode),
            $mode,
            self::FILESYSTEM_STRUCTURE
        );
    }
}
