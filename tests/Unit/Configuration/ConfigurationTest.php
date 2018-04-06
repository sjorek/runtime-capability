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

use Sjorek\RuntimeCapability\Configuration\ConfigurationManager;
use Sjorek\RuntimeCapability\Configuration\ConfigurationManagerInterface;
use Sjorek\RuntimeCapability\Management\ManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Configuration test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Configuration\AbstractConfiguration
 */
class ConfigurationTest extends AbstractTestCase
{
    /**
     * @var array
     */
    private $testData;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->testData = ['test' => 'test'];
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->testData = null;
        parent::tearDown();
    }

    /**
     * @covers ::__construct
     *
     * @return ConfigurationTestFixture
     */
    public function testConstruct(): ConfigurationTestFixture
    {
        $expect = $this->testData;
        $actual = new ConfigurationTestFixture($expect);
        $this->assertAttributeInternalType('array', 'data', $actual);
        $this->assertAttributeContains('test', 'data', $actual);
        $this->assertAttributeContainsOnly('string', 'data', $actual);
        $this->assertAttributeSame($expect, 'data', $actual);

        return $actual;
    }

    /**
     * @covers ::__construct
     */
    public function testConstructWithNonStringKeysThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid array given. Only keys of type string are allowed.');
        $this->expectExceptionCode(1522138977);

        new ConfigurationTestFixture(range(0, 9));
    }

    /**
     * @covers ::setManager
     * @covers ::setConfigurationManager
     * @depends testConstruct
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testSetManager(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $expect = new ConfigurationManager();
        $this->assertSame($actual, $actual->setManager($expect));
        $this->assertAttributeSame($expect, 'manager', $actual);
        $this->assertAttributeInstanceOf(ManagerInterface::class, 'manager', $actual);
        $this->assertAttributeInstanceOf(ConfigurationManagerInterface::class, 'manager', $actual);

        return $actual;
    }

    /**
     * @covers ::getManager
     * @covers ::getConfigurationManager
     * @depends testSetManager
     *
     * @param ConfigurationTestFixture $configuration
     */
    public function testGetManager(ConfigurationTestFixture $actual)
    {
        $this->assertAttributeSame($actual->getManager(), 'manager', $actual);
        $this->assertInstanceOf(ManagerInterface::class, $actual->getManager());
        $this->assertInstanceOf(ConfigurationManagerInterface::class, $actual->getManager());
    }

    /**
     * @covers ::export
     * @depends testConstruct
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testExport(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $expect = $this->testData;
        $this->assertSame($expect, $actual->export());

        return $actual;
    }

    /**
     * @covers ::import
     * @depends testExport
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testImport(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $expect = $this->testData;
        $actual->import($actual);

        $this->assertAttributeInternalType('array', 'data', $actual);
        $this->assertAttributeContains('test', 'data', $actual);
        $this->assertAttributeContainsOnly('string', 'data', $actual);
        $this->assertAttributeSame($expect, 'data', $actual);

        return $actual;
    }

    /**
     * @covers ::merge
     * @depends testImport
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testMerge(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $expect = $this->testData;
        $actual->merge($actual);

        $this->assertAttributeInternalType('array', 'data', $actual);
        $this->assertAttributeContains('test', 'data', $actual);
        $this->assertAttributeContainsOnly('string', 'data', $actual);
        $this->assertAttributeSame($expect, 'data', $actual);

        return $actual;
    }

    /**
     * @covers ::offetExists
     * @depends testMerge
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testOffsetExists(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $this->assertTrue(isset($actual['test']));
        $this->assertFalse(isset($actual['non-existent']));

        return $actual;
    }

    /**
     * @covers ::offsetGet
     * @depends testOffsetExists
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testOffsetGet(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $this->assertSame($this->testData['test'], $actual['test']);

        return $actual;
    }

    /**
     * @covers ::offsetSet
     * @depends testOffsetGet
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testOffsetSet(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        $actual['test2'] = 'test2';
        $this->assertAttributeContains('test2', 'data', $actual);

        return $actual;
    }

    /**
     * @covers ::offsetSet
     * @depends testOffsetGet
     *
     * @param ConfigurationTestFixture $actual
     */
    public function testOffsetSetWithNonStringKeysThrowsInvalidArgumentException(ConfigurationTestFixture $actual)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid offset given. Only keys of type string are allowed.');
        $this->expectExceptionCode(1522138977);
        $actual[0] = 0;
    }

    /**
     * @covers ::offsetUnset
     * @depends testOffsetSet
     *
     * @param ConfigurationTestFixture $actual
     *
     * @return ConfigurationTestFixture
     */
    public function testOffsetUnset(ConfigurationTestFixture $actual): ConfigurationTestFixture
    {
        unset($actual['test2']);
        $this->assertAttributeNotContains('test2', 'data', $actual);

        return $actual;
    }
}
