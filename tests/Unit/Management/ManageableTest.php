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

use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManageableTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManagerTestFixture;
use Sjorek\RuntimeCapability\Management\ManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManagementTestFixture;
use Sjorek\RuntimeCapability\Management\ManagementInterface;

/**
 * Manageable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Management\AbstractManageable
 */
class ManageableTest extends AbstractTestCase
{
    /**
     * @var ManageableTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ManageableTestFixture();
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
     * @covers ::setManager
     */
    public function testSetManager()
    {
        $instance = new ManagerTestFixture();
        $this->assertSame($this->subject, $this->subject->setManager($instance));
        $this->assertAttributeEquals($instance, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(ManagerInterface::class, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(ManagerTestFixture::class, 'manager', $this->subject);
    }

    /**
     * @covers ::getManager
     */
    public function testGetManager()
    {
        $instance = new ManagerTestFixture();
        $this->assertSame($instance, $this->subject->setManager($instance)->getManager());
    }

    /**
     * @covers ::getManager
     */
    public function testGetManagerWithMissingManagerThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing manager instance.');
        $this->expectExceptionCode(1522098121);
        $this->subject->getManager();
    }

    /**
     * @covers ::getManagement
     */
    public function testGetManagement()
    {
        $management = new ManagementTestFixture();
        $manager = (new ManagerTestFixture())->setManager($management);
        $this->assertSame($management, $this->subject->setManager($manager)->getManagement());
        $this->assertAttributeEquals($management, 'management', $this->subject);
        $this->assertAttributeInstanceOf(ManagementInterface::class, 'management', $this->subject);
        $this->assertAttributeInstanceOf(ManagementTestFixture::class, 'management', $this->subject);
    }

    /**
     * @covers ::getManagement
     */
    public function testGetManagementWithMissingManagementThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing management instance.');
        $this->expectExceptionCode(1522098124);
        $this->subject->setManager(new ManagerTestFixture())->getManagement();
    }
}
