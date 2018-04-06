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

use Sjorek\RuntimeCapability\Filesystem\Driver\DirectoryTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemHierarchyDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * PHPFilesystemHierarchyDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemHierarchyDriver
 */
class PHPFilesystemHierarchyDriverTest extends AbstractFilesystemTestCase
{
    /**
     * @covers ::__construct
     *
     * @return PHPFilesystemHierarchyDriver
     */
    public function testConstruct(): PHPFilesystemHierarchyDriver
    {
        $driver = new PHPFilesystemHierarchyDriver();
        $this->assertAttributeInstanceOf(DirectoryTargetDriverInterface::class, 'directoryDriver', $driver);
        $this->assertAttributeInstanceOf(DirectoryTargetDriver::class, 'directoryDriver', $driver);

        return $driver;
    }

    /**
     * @covers ::createDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemHierarchyDriver $driver
     */
    public function testCreateDirectory(PHPFilesystemHierarchyDriver $driver)
    {
        $path = $this->getFilesystem()->url() . '/';
        $driver->setDirectory($path);

        $this->assertDirectoryNotExists($path . 'test');
        $this->assertTrue($driver->createDirectory('test'));
        $this->assertDirectoryExists($path . 'test');
    }

    /**
     * @covers ::removeDirectory
     * @depends testConstruct
     *
     * @param PHPFilesystemHierarchyDriver $driver
     */
    public function testRemoveDirectory(PHPFilesystemHierarchyDriver $driver)
    {
        $path = $this->getFilesystem()->url() . '/';
        $driver->setDirectory($path);

        $this->assertDirectoryExists($path . 'folder');
        $this->assertTrue($driver->removeDirectory('folder'));
        $this->assertDirectoryNotExists($path . 'folder');
    }
}
