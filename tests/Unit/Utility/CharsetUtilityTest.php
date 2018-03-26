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
