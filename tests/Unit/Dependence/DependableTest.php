<?php
namespace Sjorek\RuntimeCapability\Tests\Unit\Dependence;

use Sjorek\RuntimeCapability\Dependence\DependencyManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependableTestFixture;
use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependencyManagerTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * AbstractDependable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Dependence\AbstractDependable
 */
class DependableTest extends AbstractTestCase
{
    /**
     * @var DependableTestFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DependableTestFixture();
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
     * @covers ::setManager
     * @covers ::setDependencyManager
     */
    public function testSetManager()
    {
        $instance = new DependencyManagerTestFixture();
        $this->assertSame($this->subject, $this->subject->setManager($instance));
        $this->assertAttributeSame($instance, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(DependencyManagerInterface::class, 'manager', $this->subject);
        $this->assertAttributeInstanceOf(DependencyManagerTestFixture::class, 'manager', $this->subject);
    }

    /**
     * @covers ::getManager
     * @covers ::getDependencyManager
     * @depends testSetManager
     */
    public function testGetManager()
    {
        $instance = new DependencyManagerTestFixture();
        $this->assertSame($instance, $this->subject->setManager($instance)->getManager());
    }
}

