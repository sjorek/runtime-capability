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

use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurableTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Configuration\AbstractConfigurable;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Configuration\ConfigurationInterface;

/**
 * Configurable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Configuration\AbstractConfigurable
 */
class ConfigurableTest extends AbstractTestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setProtectedProperty(
            AbstractConfigurable::class, 'DEFAULT_CONFIGURATION', ['default' => 'default']
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->setProtectedProperty(
            AbstractConfigurable::class, 'DEFAULT_CONFIGURATION', []
        );

        parent::tearDown();
    }

    /**
     * @test
     * @coversNothing
     * @doesNotPerformAssertions
     *
     * @return ConfigurableTestFixture
     */
    public function getConfigurableTestFixture(): ConfigurableTestFixture
    {
        return new ConfigurableTestFixture();
    }

    /**
     * @test
     * @coversNothing
     * @doesNotPerformAssertions
     *
     * @return ConfigurationTestFixture
     */
    public function getConfigurationTestFixture(): ConfigurationTestFixture
    {
        return new ConfigurationTestFixture(['config' => 'config']);
    }

    /**
     * @covers ::setConfiguration
     * @depends getConfigurableTestFixture
     * @depends getConfigurationTestFixture
     *
     * @param ConfigurableTestFixture $actual
     * @param ConfigurationTestFixture $expect
     * @return ConfigurableTestFixture
     */
    public function testSetConfiguration(ConfigurableTestFixture $actual, ConfigurationTestFixture $expect): ConfigurableTestFixture
    {
        $this->assertSame($actual, $actual->setConfiguration($expect));
        $this->assertAttributeInstanceOf(ConfigurationInterface::class, 'configuration', $actual);
        $this->assertAttributeInstanceOf(ConfigurationTestFixture::class, 'configuration', $actual);
        $this->assertAttributeSame($expect, 'configuration', $actual);

        return $actual;
    }

    /**
     * @covers ::getConfiguration
     * @depends testSetConfiguration
     * @depends getConfigurationTestFixture
     *
     * @param ConfigurableTestFixture $actual
     * @param ConfigurationTestFixture $expect
     * @return ConfigurableTestFixture
     */
    public function testGetConfiguration(ConfigurableTestFixture $actual, ConfigurationTestFixture $expect): ConfigurableTestFixture
    {
        $this->assertSame($expect, $actual->getConfiguration());

        return $actual;
    }

    /**
     * @covers ::config
     * @depends testGetConfiguration
     *
     * @param ConfigurableTestFixture $actual
     * @return ConfigurableTestFixture
     */
    public function testConfig(ConfigurableTestFixture $actual): ConfigurableTestFixture
    {
        $this->assertSame('config', $actual->config('config'));
        $this->assertSame('fixture', $actual->config('fixture'));
        $this->assertSame('default', $actual->config('default'));

        $this->assertSame('config', $actual->config('config', 'string'));
        $this->assertSame('fixture', $actual->config('fixture', 'string'));
        $this->assertSame('default', $actual->config('default', 'string'));

        return $actual;
    }

    /**
     * @covers ::setup
     * @depends testConfig
     *
     * @param ConfigurableTestFixture $actual
     * @return ConfigurableTestFixture
     */
    public function testSetup(ConfigurableTestFixture $actual): ConfigurableTestFixture
    {
        $this->assertSame($actual, $actual->setup());

        return $actual;
    }

    /**
     * @covers ::reset
     * @depends testSetup
     *
     * @param ConfigurableTestFixture $actual
     * @return ConfigurableTestFixture
     */
    public function testReset(ConfigurableTestFixture $actual): ConfigurableTestFixture
    {
        $this->assertSame($actual, $actual->reset());
        $this->assertAttributeSame(null, 'configuration', $actual);

        return $actual;
    }

}
