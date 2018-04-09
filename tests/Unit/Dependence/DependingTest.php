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

use Sjorek\RuntimeCapability\Tests\Fixtures\Dependence\DependingTestFixture1;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * AbstractDepending test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Dependence\AbstractDepending
 */
class DependingTest extends AbstractTestCase
{
    /**
     * @var DependingTestFixture1
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new DependingTestFixture1();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        parent::tearDown();
    }

    public function testDepends()
    {
        $this->assertSame(DependingTestFixture1::DEPENDENCIES, $this->subject->depends());
    }
}
