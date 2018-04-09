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

namespace Sjorek\RuntimeCapability\Tests\Unit\Dependence;

use Sjorek\RuntimeCapability\Dependence\DependableInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependableTestFixture;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependencyManagerTestFixture;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestCircularFixture1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestCircularFixture2;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestCircularFixture3;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestFixture1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestFixture2;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Manager test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Management\AbstractManager
 */
class DependencyManagerTest extends AbstractTestCase
{
    /**
     * @var DependencyManagerTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DependencyManagerTestFixture();
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
     * @covers ::registerDependency
     */
    public function testRegisterDependency()
    {
        $instance = new DependableTestFixture();
        $this->assertSame($instance, $this->subject->registerDependency($instance));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DependableInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DependableTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::createDependency
     * @depends testRegisterDependency
     */
    public function testCreateDependency()
    {
        $instance = $this->subject->createDependency(DependableTestFixture::class);
        $this->assertSame($instance, $this->subject->createDependency($instance->identify()));
        $this->assertAttributeSame($this->subject, 'manager', $instance);
        $this->assertAttributeContains($instance, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DependableInterface::class, 'instances', $this->subject);
        $this->assertAttributeContainsOnly(DependableTestFixture::class, 'instances', $this->subject);
    }

    /**
     * @covers ::resolveDependencies
     * @covers ::generateDependencyChain
     * @covers ::registerDependency
     * @covers ::createDependency
     * @depends testCreateDependency
     * @dataProvider provideTestResolveDependenciesData
     *
     * @param array                 $expect
     * @param DependableInterface   $dependable
     * @param DependableInterface[] $register
     */
    public function testResolveDependencies(array $expect, DependableInterface $dependable, array $register)
    {
        $manager = $this->subject;

        $this->assertSame(
            $register,
            array_map(
                function (DependableInterface $instance) use ($manager) {
                    return $manager->registerDependency($instance);
                },
                $register
            )
        );

        $actual = $this->subject->resolveDependencies($dependable);

        $this->assertInstanceOf(\Generator::class, $actual);
        $this->assertSame($expect, iterator_to_array($actual, true));
    }

    /**
     * @return array
     */
    public function provideTestResolveDependenciesData(): array
    {
        $dependable = new DependableTestFixture();
        $depending1 = new DependingTestFixture1();
        $depending2 = new DependingTestFixture2();

        return [
            'single dependency' => [
                [
                    $dependable->identify() => $dependable,
                ],
                $dependable,
                [$dependable],
            ],
            'two dependencies' => [
                [
                    $dependable->identify() => $dependable,
                    $depending1->identify() => $depending1,
                ],
                $depending1,
                [$dependable, $depending1],
            ],
            'multiple dependencies' => [
                [
                    $dependable->identify() => $dependable,
                    $depending1->identify() => $depending1,
                    $depending2->identify() => $depending2,
                ],
                $depending2,
                [$dependable, $depending1, $depending2],
            ],
        ];
    }

    /**
     * @covers ::resolveDependencies
     * @covers ::generateDependencyChain
     * @covers ::registerDependency
     * @covers ::createDependency
     * @depends testCreateDependency
     */
    public function testResolvingCircularDependenciesThrowsException()
    {
        $depending1 = new DependingTestCircularFixture1();
        $depending2 = new DependingTestCircularFixture2();
        $depending3 = new DependingTestCircularFixture3();

        $this->assertSame($depending1, $this->subject->registerDependency($depending1));
        $this->assertSame($depending2, $this->subject->registerDependency($depending2));
        $this->assertSame($depending3, $this->subject->registerDependency($depending3));

        $actual = $this->subject->resolveDependencies($depending1);
        $this->assertInstanceOf(\Generator::class, $actual);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid circular dependency for id "%s" (%s) with parent id "%s".',
                $depending1->identify(),
                get_class($depending1),
                $depending3->identify()
            )
        );
        $this->expectExceptionCode(1521250751);

        $this->assertTrue($actual->valid());
    }
}
