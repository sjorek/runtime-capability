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

use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Detection\ShellEscapeDetector;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * ShellEscapeDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\ShellEscapeDetector
 */
class ShellEscapeDetectorTest extends AbstractTestCase
{
    /**
     * @var ShellEscapeDetector
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = (new ShellEscapeDetector())->setConfiguration(new ConfigurationTestFixture());
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
     * @covers ::setup
     */
    public function testSetup()
    {
        $config = new ConfigurationTestFixture(['compact-result' => true, 'charset' => 'utf8']);
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame(false, 'compactResult', $actual);
        $this->assertAttributeSame('UTF-8', 'charset', $actual);
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithUnknownCharset()
    {
        $config = new ConfigurationTestFixture(['charset' => 'unknown']);
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame('auto', 'charset', $actual);
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithInvalidCharsetThrowsException()
    {
        $this->subject->setConfiguration(
            new ConfigurationTestFixture(['charset' => 'invalid'])
        );

        $this->expectException(ConfigurationFailure::class);
        $this->expectExceptionMessage('Invalid configuration value for key "charset": invalid');
        $this->expectExceptionCode(1521291497);

        $this->subject->setup();
    }

    /**
     * @covers ::evaluate
     */
    public function _testEvaluate(array $dependencyResults)
    {
        $this->subject->setDependencyResults(...$dependencyResults);
        $this->assertInternalType('boolean', $this->subject->detect());
    }
}
