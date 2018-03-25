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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\Encoding;

use Sjorek\RuntimeCapability\Detection\LocaleCharsetDetector;
use Sjorek\RuntimeCapability\Detection\DefaultCharsetDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\FilesystemEncodingDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Sjorek\RuntimeCapability\Utility\CharsetUtility;
use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements FilesystemEncodingDetectorInterface
{
    /**
     * @var string[]
     */
    const DEPENDENCIES = [
        LocaleCharsetDetector::class,
        DefaultCharsetDetector::class,
    ];

    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriver::class,
        'filesystem-encoding' => 'UTF8',
        'filename-tests' => self::UTF8_FILENAME_TESTS,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var string
     */
    protected $filesystemEncoding = null;

    /**
     * @var string[]|bool[]
     */
    protected $filenameTests = [];

    /**
     * @var string
     */
    protected $filenameDetectionPattern = '%s-%s';

    /**
     * {@inheritdoc}
     *
     * @see AbstractFilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
        $filesystemEncoding = $this->config('filesystem-encoding', 'string');
        if (!in_array($filesystemEncoding, CharsetUtility::getEncodings(), true)) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration value for key "filesystem-encoding": %s', $filesystemEncoding),
                1521291497
            );
        }
        $this->filesystemEncoding = $filesystemEncoding;

        $this->filenameTests = $this->config('filename-tests', 'array');

        $this->filenameDetectionPattern =
            $this->config(
                'filename-detection-pattern',
                'match:^[A-Za-z0-9_.-]{1,100}(?:%s[A-Za-z0-9_.-]{0,10}){2}$'
            )
        ;

        return $this;
    }

    /**
     * Detect utf8-capabilities.
     *
     * The result will look like following example:
     * <pre>
     * php > [
     * php >      NormalizationForms::NONE => true,
     * php >      NormalizationForms::NFC => true,
     * php >      NormalizationForms::NFD => true,
     * php >      NormalizationForms::NFKC => true,
     * php >      NormalizationForms::NFKC => true,
     * php >      NormalizationForms::NFD_MAC => false,
     * php > ]
     * </pre>
     *
     * {@inheritdoc}
     *
     * @param array $localeCharset
     * @param string $defaultCharset
     * @return array[]|boolean[]|boolean[]
     * @see \Sjorek\RuntimeCapability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluate(array $localeCharset, string $defaultCharset)
    {
        $charset = $this->filesystemEncoding;
        if ($charset === $localeCharset[LC_CTYPE] || $charset === $defaultCharset) {
            return [
                $charset => $this->testFilesystem(array_map(function () { return null; }, $this->filenameTests))
            ];
        }

        return [$charset => false];
    }

    /**
     * @param array $encodings
     * @param array $tests
     *
     * @return array
     */
    protected function testFilesystem(array $tests): array
    {
        foreach ($this->filenameTests as $index => $testString) {
            if (false === $testString || !isset($tests[$index])) {
                continue;
            }
            $fileName = $this->generateDetectionFileNameForIndex($index, hex2bin($testString));
            try {
                $tests[$index] =
                    $this->filesystemDriver->create($fileName) &&
                    $this->filesystemDriver->exists($fileName) &&
                    $this->filesystemDriver->remove($fileName)
                ;
            } catch (IOExceptionInterface $e) {
                $tests[$index] = false;
            }
        }

        return $tests;
    }

    /**
     * @param int    $index
     * @param string $testString
     * @return string
     */
    protected function generateDetectionFileNameForIndex($index, $testString)
    {
        $fileName = sprintf($this->filenameDetectionPattern, $index, $testString);
        if (null === $this->filesystemEncoding && 'UTF8' === $this->filesystemEncoding) {
            return $fileName;
        }

        return mb_convert_encoding($fileName, $this->filesystemEncoding, 'UTF8');
    }
}
