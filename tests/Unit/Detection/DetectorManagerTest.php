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

use Sjorek\RuntimeCapability\Detection\DetectorInterface;
use Sjorek\RuntimeCapability\Detection\DetectorManager;
use Sjorek\RuntimeCapability\Tests\Fixtures\Detection\DependingDetectorTestFixture1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Detection\DetectorTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * DetectorManager test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\DetectorManager
 */
class DetectorManagerTest extends AbstractTestCase
{
    /**
     * @var DetectorManager
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DetectorManager();
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
     * @covers ::registerDetector
     */
    public function testRegisterDetector()
    {
        $instance = new DetectorTestFixture();
        $this->assertSame($instance, $this->subject->registerDetector($instance));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DetectorInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DetectorTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createDetector
     * @depends testRegisterDetector
     */
    public function testCreateDetector()
    {
        $instance = $this->subject->createDetector(DetectorTestFixture::class);
        $this->assertSame($instance, $this->subject->createDetector($instance->identify()));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DetectorInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DetectorTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::resolveDetectorDependencies
     * @depends testCreateDetector
     * @dataProvider provideTestResolveDetectorDependenciesData
     *
     * @param array               $expect
     * @param DetectorInterface   $detector
     * @param DetectorInterface[] $register
     */
    public function testResolveDetectorDependencies(array $expect, DetectorInterface $detector, array $register)
    {
        $manager = $this->subject;

        $this->assertSame(
            $register,
            array_map(
                function (DetectorInterface $instance) use ($manager) {
                    return $manager->registerDetector($instance);
                },
                $register
            )
        );

        $actual = $this->subject->resolveDetectorDependencies($detector);

        $this->assertInstanceOf(\Generator::class, $actual);
        $this->assertSame($expect, iterator_to_array($actual, true));
    }

    /**
     * @return array
     */
    public function provideTestResolveDetectorDependenciesData(): array
    {
        $fixture1 = new DetectorTestFixture();
        $fixture2 = new DependingDetectorTestFixture1();

        return [
            'single dependency' => [
                [
                    $fixture1->identify() => $fixture1,
                ],
                $fixture1,
                [$fixture1],
            ],
            'two dependencies' => [
                [
                    $fixture1->identify() => $fixture1,
                    $fixture2->identify() => $fixture2,
                ],
                $fixture2,
                [$fixture1, $fixture2],
            ],
        ];
    }
}
