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

use Sjorek\RuntimeCapability\Utility\CharsetUtility;
use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class ShellEscapeDetector extends AbstractDependingDetector
{
    /**
     * @var string[]
     */
    const DEPENDENCIES = [
        PlatformDetector::class,
        LocaleCharsetDetector::class,
        DefaultCharsetDetector::class,
    ];

    /**
     * php > echo bin2hex('äöü');.
     *
     * @var string
     */
    const TEST_STRING = 'c3a4c3b6c3bc';

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'charset' => 'UTF8',
    ];

    /**
     * @var string
     */
    protected $charset = null;

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

        $charset = $this->config('charset', 'string');
        if (!in_array($charset, CharsetUtility::getEncodings(), true)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration value for key "charset": %s', $charset),
                1521291497
            );
        }
        $this->charset = $charset;

        return $this;
    }

    /**
     * Escapeshellarg uses the 'default_charset' configuration on platforms lacking a 'mblen'-implementation,
     * since PHP 5.6.0,.
     *
     * {@inheritdoc}
     *
     * @param string $platform
     * @param string $phpVersion
     * @param string $defaultCharset
     *
     * @return bool
     *
     * @see AbstractDetector::evaluate()
     * @see http://www.php.net/manual/en/function.escapeshellarg.php#refsect1-function.escapeshellarg-changelog
     * @see https://github.com/php/php-src/blob/PHP-5.6.0/ext/standard/exec.c#L349
     * @see https://github.com/php/php-src/blob/PHP-5.6.0/ext/standard/php_string.h#L155
     * @see http://man7.org/linux/man-pages/man3/mblen.3.html
     * @see https://www.freebsd.org/cgi/man.cgi?query=mblen
     * @see http://man.openbsd.org/mblen.3
     * @see https://developer.apple.com/legacy/library/documentation/Darwin/Reference/ManPages/man3/mblen_l.3.html
     * @see https://developer.apple.com/library/content/documentation/General/Reference/APIDiffsMacOSX10_10SeedDiff/modules/Darwin.html
     * @see https://docs.microsoft.com/en-us/cpp/c-runtime-library/reference/mbclen-mblen-mblen-l
     */
    protected function evaluate(array $platform, array $localeCharset, string $defaultCharset)
    {
        $testString = hex2bin(self::TEST_STRING);
        $quote = 'Windows' === $platform['os-family'] ? '"' : '\'';

        return
            (
                $this->charset === $localeCharset[LC_CTYPE] ||
                ($this->charset === $defaultCharset || $platform['version-id'] < 50600)
            ) &&
            escapeshellarg($testString) === $quote . $testString . $quote
        ;
    }
}
