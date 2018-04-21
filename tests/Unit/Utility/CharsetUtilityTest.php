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

use Sjorek\RuntimeCapability\Utility\CharsetUtility;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractLocaleTestCase;
use PHPUnit\Framework\Error\Warning;

/**
 * CharsetUtility test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Utility\CharsetUtility
 */
class CharsetUtilityTest extends AbstractLocaleTestCase
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
     * @uses ::languageInfo
     * @testWith ["ASCII", "C"]
     *           ["UTF-8", "de_DE.utf8"]
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
        $namespace = $this->getCharsetUtilityNamespace();
        $GLOBALS[$namespace]['setlocale'][LC_CTYPE] = null !== $expect ? $locale : false;
        $this->assertSame($expect, CharsetUtility::getEncodingNameFromLocaleString($locale));
        unset($GLOBALS[$namespace]['setlocale']);
    }

    /**
     * @covers ::languageInfo
     * @testWith ["", "C"]
     *           ["utf8", "de_DE.utf8"]
     *           ["utf-8", "de_DE.utf-8"]
     *           ["UTF8", "de_DE.UTF8"]
     *           ["UTF-8", "de_DE.UTF-8"]
     *           ["UTF-8", "de_DE.UTF-8@euro"]
     *           ["65001", "de_DE.65001"]
     *           ["65001", "de_DE.65001@euro"]
     *           ["", "de_DE@euro"]
     *
     * @param string|null $expect
     * @param string      $locale
     */
    public function testLanguageInfo(?string $expect, string $locale)
    {
        $namespace = $this->getCharsetUtilityNamespace();
        $GLOBALS[$namespace]['setlocale'][LC_CTYPE] = $locale;
        $this->assertSame($expect, CharsetUtility::languageInfo(CODESET));
        unset($GLOBALS[$namespace]['setlocale']);
    }

    /**
     * @covers ::languageInfo
      */
    public function testLanguageInfoTriggersWarningForUnsupportedItem()
    {
        $this->expectException(Warning::class);
        $this->expectExceptionMessage("Warning: nl_langinfo(): Item '-1' is not valid");

        CharsetUtility::languageInfo(-1);
    }
}
