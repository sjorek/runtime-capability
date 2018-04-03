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

namespace Sjorek\RuntimeCapability\Tests\Unit;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Filesystem test case.
 */
abstract class AbstractFilesystemTestCase extends AbstractTestCase
{
    /**
     * @var string[]
     */
    const FILESYSTEM_STRUCTURE = [
        'file' => 'file',
        'folder' => [],
        'symlink' => 'symlink',
        'dangling-symlink' => 'dangling-symlink',
    ];

    /**
     * @var int
     */
    const FILESYSTEM_MODE_FULL_ACCESS = 0777;

    /**
     * @var int
     */
    const FILESYSTEM_MODE_READ_ONLY = 0555;

    /**
     * @var int
     */
    const FILESYSTEM_MODE_NOT_EXECUTEABLE = 0666;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        require_once str_replace(
            ['/Unit/', 'AbstractFilesystemTestCase.php'],
            ['/Fixtures/', 'FilesystemTestFixture.php'],
            __FILE__
        );
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

    // ////////////////////////////////////////////////////////////////
    // utility methods
    // ////////////////////////////////////////////////////////////////

    /**
     * @param int $mode
     * @param array $structure
     * @return vfsStreamDirectory
     */
    protected function getFilesystem(int $mode = self::FILESYSTEM_MODE_FULL_ACCESS,
                                     array $structure = self::FILESYSTEM_STRUCTURE): vfsStreamDirectory
    {
        vfsStreamWrapper::unregister();
        return vfsStream::setup(sprintf('root-%o', $mode), $mode, $structure);
    }
}
