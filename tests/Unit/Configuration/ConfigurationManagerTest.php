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

namespace Sjorek\RuntimeCapability\Tests\Unit\Configuration;

use Sjorek\RuntimeCapability\Configuration\ConfigurationInterface;
use Sjorek\RuntimeCapability\Configuration\ConfigurationManager;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Configuration test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Configuration\ConfigurationManager
 */
class ConfigurationManagerTest extends AbstractTestCase
{
    /**
     * @var ConfigurationManager
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ConfigurationManager();
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
        $instance = new ConfigurationTestFixture();
        $this->assertSame($instance, $this->subject->registerConfiguration($instance));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ConfigurationInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ConfigurationTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createManager
     */
    public function testCreateManager()
    {
        $instance = $this->subject->createConfiguration(ConfigurationTestFixture::class);
        $this->assertSame($instance, $this->subject->createConfiguration($instance->identify()));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ConfigurationInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(ConfigurationTestFixture::class, 'instances', $this->subject);
    }
}
