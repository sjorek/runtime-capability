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

namespace Sjorek\RuntimeCapability\Tests\Unit\Identification;

use Sjorek\RuntimeCapability\Identification\AbstractIdentifiable;
use Sjorek\RuntimeCapability\Tests\Fixtures\Identification\IdentifiableTestFixture1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Identification\IdentifiableTestFixture2;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Identification\AbstractIdentifiable
 */
class IdentifiableTest extends AbstractTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Identification\AbstractIdentifiable
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new IdentifiableTestFixture1();
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
     * @covers ::identify
     */
    public function testIdentify()
    {
        $this->assertSame('identifiable-test', $this->subject->identify());
        $this->assertSame('custom-identifier', (new IdentifiableTestFixture2())->identify());
    }

    /**
     * @covers       ::extractIdentifier
     * @dataProvider provideTestExtractIdentifierData
     *
     * @param string $expect
     * @param string $input
     */
    public function testExtractIdentifier(string $expect, string $input)
    {
        $this->assertSame(
            $expect,
            $this->callProtectedMethod($this->subject, 'extractIdentifier', $input)
        );
    }

    /**
     * @return string[]
     */
    public function provideTestExtractIdentifierData()
    {
        return $this->extractTestDataFromDocComment(
            (new \ReflectionMethod(AbstractIdentifiable::class, 'extractIdentifier'))->getDocComment()
        );
    }

    /**
     * @covers       ::normalizeIdentifier
     * @dataProvider provideTestNormalizeIdentifierData
     *
     * @param string $expect
     * @param string $input
     */
    public function testNormalizeIdentifier(string $expect, string $input)
    {
        $this->assertSame(
            $expect,
            $this->callProtectedMethod($this->subject, 'normalizeIdentifier', $input)
        );
    }

    /**
     * @return string[]
     */
    public function provideTestNormalizeIdentifierData()
    {
        return $this->extractTestDataFromDocComment(
            (new \ReflectionMethod(AbstractIdentifiable::class, 'normalizeIdentifier'))->getDocComment()
        );
    }
}
