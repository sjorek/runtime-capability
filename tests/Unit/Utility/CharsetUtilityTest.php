<?php

namespace Sjorek\RuntimeCapability\Tests\Unit\Utility;

use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Utility\CharsetUtility;

/**
 * CharsetUtility test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Utility\CharsetUtility
 */
class CharsetUtilityTest extends AbstractTestCase
{
    /**
     * @covers ::getEncodings
     */
    public function testGetEncodings()
    {
        $actual = CharsetUtility::getEncodings();
        $this->assertNotEmpty($actual);
        $this->assertContainsOnly('string', $actual);
        $this->assertContains('UTF8', $actual);
        $this->assertContains('UTF-8', $actual);
        $this->assertNotContains('utf8', $actual);
        $this->assertNotContains('utf-8', $actual);
    }
}

