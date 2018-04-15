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
     * @covers ::normalizeEncodingName
     * @testWith ["UTF-8", "utf8"]
     *           ["auto", "unknown"]
     *           [null, "invalid"]
     *
     * @param string|null $expect
     * @param string      $charset
     */
    public function testNormalizEncodingNamee(?string $expect, string $charset)
    {
        $this->assertSame($expect, CharsetUtility::normalizeEncodingName($charset));
    }

    /**
     * @covers ::getEncodingNameFromLocaleString
     * @testWith ["UTF-8", "de_DE.utf8"]
     *           ["UTF-8", "de_DE.utf-8"]
     *           ["UTF-8", "de_DE.UTF8"]
     *           ["UTF-8", "de_DE.UTF-8"]
     *           ["UTF-8", "de_DE.UTF-8@euro"]
     *           ["UTF-8", "de_DE.65001"]
     *           ["UTF-8", "de_DE.65001@euro"]
     *           [null, "de_DE@euro"]
     *
     * @param string|null $expect
     * @param string      $locale
     */
    public function testGetEncodingNameFromLocaleString(?string $expect, string $locale)
    {
        $this->assertSame($expect, CharsetUtility::getEncodingNameFromLocaleString($locale));
    }
}
