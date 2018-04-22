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

use Sjorek\RuntimeCapability\Detection\PlatformDetector;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * PlatformDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\PlatformDetector
 */
class PlatformDetectorTest extends AbstractTestCase
{
    /**
     * @var PlatformDetector
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = (new PlatformDetector())->setConfiguration(new ConfigurationTestFixture());
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
        $config = new ConfigurationTestFixture(['compact-result' => true]);
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame(false, 'compactResult', $actual);
    }

    /**
     * @covers ::evaluate
     */
    public function testEvaluate()
    {
        $actual = $this->subject->detect();
        $this->assertSame(['name', 'binary', 'os', 'os-family', 'version', 'version-id'], array_keys($actual));
    }
}
