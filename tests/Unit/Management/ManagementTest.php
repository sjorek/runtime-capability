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

namespace Sjorek\RuntimeCapability\Tests\Unit\Management;

use Sjorek\RuntimeCapability\Management\ManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManagementTestFixture;
use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManagerTestFixture;

/**
 * Management test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Management\AbstractManagement
 */
class ManagementTest extends ManagerTest
{
    /**
     * @var ManagementTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ManagementTestFixture();
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
        $instance = new ManagerTestFixture();
        $this->assertSame($instance, $this->subject->registerManager($instance));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManagerInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManagerTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createManager
     */
    public function testCreateManager()
    {
        $instance = $this->subject->createManager(ManagerTestFixture::class);
        $this->assertSame($instance, $this->subject->createManager($instance->identify()));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManagerInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManagerTestFixture::class, 'instances', $this->subject);
    }
}
