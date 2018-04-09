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

use Sjorek\RuntimeCapability\Tests\Fixtures\Detection\DependingDetectorTestFixture1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Detection\DependingDetectorTestFixture2;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * AbstractDependingDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\AbstractDependingDetector
 */
class DependingDetectorTest extends AbstractTestCase
{
    /**
     * @var DependingDetectorTestFixture1
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DependingDetectorTestFixture1();
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
     * @covers ::setDependencyResults
     */
    public function testSetDependencyResults()
    {
        $this->assertSame($this->subject, $this->subject->setDependencyResults(true));
        $this->assertAttributeContainsOnly('boolean', 'dependencyResults', $this->subject);
        $this->assertAttributeContains(true, 'dependencyResults', $this->subject);
        $this->assertAttributeSame([true], 'dependencyResults', $this->subject);
    }

    /**
     * @covers ::evaluate
     * @covers ::evaluateWithDependency
     * @depends testSetDependencyResults
     */
    public function testEvaluate()
    {
        $this->assertTrue($this->subject->setDependencyResults(true)->detect());
    }

    /**
     * @covers ::evaluateWithDependency
     * @depends testSetDependencyResults
     */
    public function testEvaluateWithDependencyThrowsErrorIfImplementationIsMissing()
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf('Call to undefined method %s::evaluateWithDependency()', DependingDetectorTestFixture2::class)
        );

        $subject = new DependingDetectorTestFixture2();
        $subject->detect();
    }
}
