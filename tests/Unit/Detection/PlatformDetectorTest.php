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

use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Detection\PlatformDetector;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;

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

        $this->subject = new PlatformDetector();
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
     * @covers ::evaluate
     */
    public function testEvaluate()
    {
        $actual = $this->subject->setConfiguration(new ConfigurationTestFixture())->detect();
        $this->assertSame(['name', 'binary', 'os', 'os-family', 'version', 'version-id'], array_keys($actual));
    }
}
