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

use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManagerTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Tests\Fixtures\Management\ManageableTestFixture;
use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * Manager test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Management\AbstractManager
 */
class ManagerTest extends AbstractTestCase
{
    /**
     * @var ManagerTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ManagerTestFixture();
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
    public function testRegisterManageable()
    {
        $instance = new ManageableTestFixture();
        $this->assertSame($instance, $this->subject->registerManageable($instance));
        $this->assertAttributeEquals($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManageableInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManageableTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createManageable
     */
    public function testCreateManageable()
    {
        $instance = $this->subject->createManageable(ManageableTestFixture::class);
        $this->assertSame($instance, $this->subject->createManageable($instance->identify()));
        $this->assertAttributeEquals($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManageableInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ManageableTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createManageable
     */
    public function testCreateManageableWithNonExistentClassThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The class does not exist: Non\\Existent');
        $this->expectExceptionCode(1521207163);
        $this->subject->createManageable('Non\\Existent');
    }

    /**
     * @covers ::createManageable
     */
    public function testCreateManageableWithInvalidClassThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The class does implement the interface "%s": stdClass',
                ManageableInterface::class
            )
        );
        $this->expectExceptionCode(1521207167);
        $this->subject->createManageable(\stdClass::class);
    }
}
