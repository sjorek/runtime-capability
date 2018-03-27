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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver;

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManager;
use Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\FilesystemDriverTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Configuration test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManager
 */
class FilesystemDriverManagerTest extends AbstractTestCase
{
    /**
     * @var FilesystemDriverManager
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new FilesystemDriverManager();
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
     * @covers ::registerManageable
     */
    public function testRegisterManager()
    {
        $instance = new FilesystemDriverTestFixture();
        $this->assertSame($instance, $this->subject->registerFilesystemDriver($instance));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(FilesystemDriverInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(FilesystemDriverTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createManager
     */
    public function testCreateManager()
    {
        $instance = $this->subject->createFilesystemDriver(FilesystemDriverTestFixture::class);
        $this->assertSame($instance, $this->subject->createFilesystemDriver($instance->identify()));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(FilesystemDriverInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(FilesystemDriverTestFixture::class, 'instances', $this->subject);
    }
}
