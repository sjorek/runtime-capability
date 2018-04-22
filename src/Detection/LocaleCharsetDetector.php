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

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
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
        'locale-categories' => self::LOCALE_CATEGORIES,
        'empty-locale-on-windows-is-valid' => true,
    ];

    /**
     * @var int[]
     */
    protected $localeCategories = [];

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
    public function setup(): ConfigurableInterface
    {
        parent::setup();

        $categories = $this->config('locale-categories', 'array');
        if (empty($categories)) {
            throw new ConfigurationFailure(
                'Missing configuration values for key "locale-categories"',
                1524298141
            );
        }
        if ($invalid = array_diff($categories, static::LOCALE_CATEGORIES)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration values for key "locale-categories": %s', implode(',', $invalid)),
                1524298144
            );
        }
        $this->localeCategories = $categories;

        $this->emptyLocaleOnWindowsIsValid = $this->config('empty-locale-on-windows-is-valid', 'boolean');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluateWithDependency(array $platform)
    {
        $capabilities = [];
        foreach ($this->localeCategories as $category) {
            $capabilities[$category] = false;
            $locale = setlocale($category, 0);
            if (false === $locale) {
                continue;
            }
            if ('' === $locale) {
                if ('Windows' === $platform['os-family']) {
                    $capabilities[$category] = $this->emptyLocaleOnWindowsIsValid;
                }
                continue;
            }
            $charset = CharsetUtility::getEncodingNameFromLocaleString($locale);
            if (null === $charset) {
                continue;
            }
            $capabilities[$category] = $charset;
        }

        return $capabilities;
    }
}
