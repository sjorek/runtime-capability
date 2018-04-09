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

namespace Sjorek\RuntimeCapability\Detection;

use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Utility\CharsetUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class LocaleCharsetDetector extends AbstractDependingDetector
{
    /**
     * @var string[]
     */
    const DEPENDENCIES = [PlatformDetector::class];

    /**
     * @var int[]
     */
    const LOCALE_CATEGORIES = [LC_ALL, LC_COLLATE, LC_CTYPE, LC_MESSAGES, LC_MONETARY, LC_NUMERIC, LC_TIME];

    /**
     * @var array
     */
    protected static $DEFAULT_CONFIGURATION = [
        'locale-categories' => static::LOCALE_CATEGORIES,
        'locale-charset' => 'UTF8',
        'windows-codepage' => CharsetUtility::WINDOWS_CODEPAGE_UTF8,
        'empty-locale-on-windows-is-valid' => true,
    ];

    /**
     * @var int[]
     */
    protected $localeCategories = [];

    /**
     * @var string
     */
    protected $localeCharset = null;

    /**
     * @var string
     */
    protected $windowsCodepage = null;

    /**
     * On platform Windows an empty locale value falls back to the “implementation-defined native
     * environment”. This usually means, it is defined by the regional settings from the Control-Panel.
     *
     * @var bool
     *
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/locale-names-languages-and-country-region-strings
     */
    protected $emptyLocaleOnWindowsIsValid = false;

    /**
     * {@inheritdoc}
     *
     * @throws ConfigurationFailure
     *
     * @see AbstractDetector::setup()
     */
    public function setup()
    {
        parent::setup();

        $categories = $this->config('locale-categories', 'array');
        if ($invalid = array_diff($categories, static::LOCALE_CATEGORIES)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration values for key "locale-categories": %s', implode(',', $invalid)),
                1521291497
            );
        }
        $this->localeCategories = $categories;

        $charset = $this->config('locale-charset', 'string');
        if (!in_array($charset, CharsetUtility::getEncodings(), true)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration value for key "locale-charset": %s', $charset),
                1521291498
            );
        }
        $this->localeCharset = $charset;

        $codepage = $this->config('locale-charset', 'string');
        if (!in_array($codepage, CharsetUtility::WINDOWS_CODEPAGES, true)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration value for key "windows-codepage": %s', $codepage),
                1521291499
            );
        }
        $this->windowsCodepage = $codepage;

        $this->emptyLocaleOnWindowsIsValid = $this->config('empty-locale-on-windows-is-valid', 'boolean');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate(array $platform)
    {
        $capabilities = [];
        foreach ($this->categories as $category) {
            $locale = setlocale($category, 0);
            $capabilities[$category] = false;
            if (false === $locale) {
                continue;
            }
            if (false !== stripos(strtr($locale, '-', ''), '.' . strtr($this->localeCharset, '-', ''))) {
                $capabilities[$category] = $this->localeCharset;
                continue;
            }
            if ('Windows' === $platform['os-family']) {
                if ('' === $locale) {
                    if ($this->emptyLocaleOnWindowsIsValid) {
                        $capabilities[$category] = $this->localeCharset;
                    }
                    continue;
                }
                if (false !== strpos($locale, '.' . $this->windowsCodepage)) {
                    $capabilities[$category] = $this->localeCharset;
                    continue;
                }
            }
        }

        return $capabilities;
    }
}
