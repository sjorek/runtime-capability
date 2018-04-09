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

namespace Sjorek\RuntimeCapability\Tests\Unit\Detection;

use Sjorek\RuntimeCapability\Configuration\ConfigurationInterface;
use Sjorek\RuntimeCapability\Detection\DetectorManager;
use Sjorek\RuntimeCapability\Detection\DetectorManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Fixtures\Detection\DetectorTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * AbstractDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\AbstractDetector
 */
class DetectorTest extends AbstractTestCase
{
    /**
     * @var DetectorTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DetectorTestFixture();
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
     * @covers ::setDetectorManager
     * @covers ::setDependencyManager
     */
    public function testSetManager()
    {
        $instance = new DetectorManager();
        $this->assertSame($this->subject, $this->subject->setManager($instance));
        $this->assertAttributeSame($instance, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(DetectorManagerInterface::class, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(DetectorManager::class, 'manager', $this->subject);
    }

    /**
     * @covers ::getDetectorManager
     * @covers ::getDependencyManager
     * @depends testSetManager
     */
    public function testGetManager()
    {
        $instance = new DetectorManager();
        $this->assertSame($instance, $this->subject->setManager($instance)->getManager());
    }

    /**
     * @covers ::setup
     * @dataProvider provideTestSetupData
     *
     * @param ConfigurationInterface $configuration
     */
    public function testSetup(bool $expect, ConfigurationInterface $configuration)
    {
        $this->subject->setConfiguration($configuration);
        $this->assertSame($this->subject, $this->subject->setup());
        $this->assertAttributeSame($expect, 'compactResult', $this->subject);
    }

    /**
     * @return array
     */
    public function provideTestSetupData(): array
    {
        return [
            'empty configuration uses default' => [
                false, new ConfigurationTestFixture(),
            ],
            'configuration with compact-result enabled' => [
                true, new ConfigurationTestFixture(['compact-result' => true]),
            ],
            'configuration with compact-result disabled' => [
                false, new ConfigurationTestFixture(['compact-result' => false]),
            ],
        ];
    }

    /**
     * @covers ::reduceResult
     * @dataProvider provideTestReduceResultData
     *
     * @param bool|bool[]  $expect
     * @param array|bool[] $actual
     */
    public function testReduceResult($expect, array $actual)
    {
        $this->assertSame($expect, $this->callProtectedMethod($this->subject, 'reduceResult', $actual));
    }

    /**
     * @return array
     */
    public function provideTestReduceResultData(): array
    {
        return [
            'single level true' => [
                true,
                [true],
            ],
            'single level false' => [
                false,
                [false],
            ],
            'single level mixed boolean' => [
                [true, false],
                [true, false],
            ],
            'single level non-boolean' => [
                [0, 1],
                [0, 1],
            ],
            'double level true' => [
                true,
                [[true], [true]],
            ],
            'double level false' => [
                false,
                [[false], [false]],
            ],
            'double level mixed boolean' => [
                [true, false],
                [[true], [false]],
            ],
            'double level mixed mixed boolean' => [
                [[true, false], [true, false]],
                [[true, false], [true, false]],
            ],
            'double level true and mixed non-boolean' => [
                [true, [0, 1]],
                [[true, true], [0, 1]],
            ],
            'triple level' => [
                [[[true]], [[true]]],
                [[[true]], [[true]]],
            ],
        ];
    }

    /**
     * @covers ::detect
     * @depends testSetup
     * @depends testReduceResult
     */
    public function testDetect()
    {
        $this->subject->setConfiguration(new ConfigurationTestFixture());
        $this->assertTrue($this->subject->detect());
    }
}
