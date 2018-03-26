<?php

namespace Sjorek\RuntimeCapability\Tests\Unit\Identification;

use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Tests\Unit\Fixtures\IdentifiableFixture;

/**
 * IdentifiableFixture test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Identification\AbstractIdentifiable
 */
class IdentifiableTest extends AbstractTestCase
{
    /**
     *
     * @var IdentifiableFixture
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new IdentifiableFixture();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        parent::tearDown();
    }

    public function testIdentify()
    {
        $this->assertSame('identifiable', $this->subject->identify());
    }

    /**
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
            (new \ReflectionMethod(IdentifiableFixture::class, 'extractIdentifier'))->getDocComment()
        );
    }

    /**
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
            (new \ReflectionMethod(IdentifiableFixture::class, 'normalizeIdentifier'))->getDocComment()
        );
    }

}

