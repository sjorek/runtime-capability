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

namespace Sjorek\RuntimeCapability\Capability\Detection;

use Sjorek\RuntimeCapability\Exception\CapabilityDetectionFailure;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class LocaleDetector extends AbstractDetector
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
     * On Windows the codepage 65001 refers to UTF-8.
     *
     * @var string
     *
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/locale-names-languages-and-country-region-strings
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/reference/setlocale-wsetlocale
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/code-pages
     * @see https://msdn.microsoft.com/library/windows/desktop/dd317756.aspx
     */
    const UTF8_CODEPAGE_WINDOWS = '65001';

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'locale-categories' => static::LOCALE_CATEGORIES,
        'empty-locale-on-windows-is-valid' => true,
    ];

    /**
     * @var int[]
     */
    protected $categories = [];

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
     * @see AbstractDetector::setup()
     */
    public function setup(array &$configuration)
    {
        parent::setup($configuration);

        $categories = $this->getConfiguration('categories', 'array');
        if ($invalid = array_diff($categories, static::LOCALE_CATEGORIES)) {
            throw new CapabilityDetectionFailure(
                sprintf('Invalid configuration values for key "categories": %s', implode(',', $invalid)),
                1521291497
            );
        }
        $this->categories = $categories;

        $this->emptyLocaleOnWindowsIsValid = $this->getConfiguration('empty-locale-on-windows-is-valid', 'boolean');
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
            if (false === $locale) {
                $capabilities[$category] = false;
                continue;
            }
            if (false !== stripos(strtr($locale, '-', ''), '.UTF8')) {
                $capabilities[$category] = true;
                continue;
            }
            if ('Windows' === $platform['os-family']) {
                if ('' === $locale) {
                    $capabilities[$category] = $this->emptyLocaleOnWindowsIsValid;
                    continue;
                }
                if (false !== strpos($locale, '.' . self::UTF8_CODEPAGE_WINDOWS)) {
                    $capabilities[$category] = true;
                    continue;
                }
            }
            $capabilities[$category] = false;
        }

        return $capabilities;
    }
}
