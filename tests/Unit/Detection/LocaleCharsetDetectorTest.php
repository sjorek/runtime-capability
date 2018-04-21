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

use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Detection\LocaleCharsetDetector;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractLocaleTestCase;

/**
 * LocaleCharsetDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\LocaleCharsetDetector
 */
class LocaleCharsetDetectorTest extends AbstractLocaleTestCase
{
    /**
     * @var LocaleCharsetDetector
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        require_once str_replace(
            ['/Unit/', '.php'],
            ['/Fixtures/', 'Fixture.php'],
            __FILE__
        );

        $this->subject = (new LocaleCharsetDetector())->setConfiguration(new ConfigurationTestFixture());
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
     * @covers ::setup
     */
    public function testSetup()
    {
        $config = new ConfigurationTestFixture(
            [
                // 'compact-result' => true,
                'locale-categories' => [LC_ALL],
                // 'empty-locale-on-windows-is-valid' => true,
            ]
        );
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame(false, 'compactResult', $actual);
        $this->assertAttributeSame([LC_ALL], 'localeCategories', $actual);
        $this->assertAttributeSame(true, 'emptyLocaleOnWindowsIsValid', $actual);
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithMissingLocaleCategoriesThrowsException()
    {
        $this->subject->setConfiguration(
            new ConfigurationTestFixture(['locale-categories' => []])
        );

        $this->expectException(ConfigurationFailure::class);
        $this->expectExceptionMessage('Missing configuration values for key "locale-categories"');
        $this->expectExceptionCode(1524298141);

        $this->subject->setup();
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithInvalidLocaleCategoriesThrowsException()
    {
        $this->subject->setConfiguration(
            new ConfigurationTestFixture(['locale-categories' => [LC_ALL, -1]])
        );

        $this->expectException(ConfigurationFailure::class);
        $this->expectExceptionMessage('Invalid configuration values for key "locale-categories": -1');
        $this->expectExceptionCode(1524298144);

        $this->subject->setup();
    }

    /**
     * @covers ::evaluateWithDependency
     * @testWith ["UTF-8", "de_DE.utf8", "UTF-8", null]
     *           [false, false, "", null]
     *           [false, "", "", "Linux"]
     *           [true, "", "", "Windows"]
     *
     * @param boolean|string $expect
     * @param boolean|string $locale
     * @param string $charset
     * @param string|null $osFamily
     */
    public function testEvaluateWithDependency($expect, $locale, $charset, ?string $osFamily)
    {
        $this->subject->setup();

        $namespace = $this->getCharsetUtilityNamespace();
        $GLOBALS[$namespace]['setlocale'] = array_map(
            function() use($locale) {
                return $locale;
            },
            array_flip(LocaleCharsetDetector::LOCALE_CATEGORIES)
        );
        $GLOBALS[$namespace]['nl_langinfo'][CODESET] = $charset;

        $this->subject->setDependencyResults(['os-family' => $osFamily ?: '']);

        $actual = $this->subject->detect();
        $this->assertInternalType('array', $actual);
        $this->assertSame(
            array_map(
                function() use($expect) {
                    return $expect;
                },
                array_flip(LocaleCharsetDetector::LOCALE_CATEGORIES)
            ),
            $actual
        );

        unset($GLOBALS[$namespace]['setlocale']);
        unset($GLOBALS[$namespace]['nl_langinfo']);
    }
}
