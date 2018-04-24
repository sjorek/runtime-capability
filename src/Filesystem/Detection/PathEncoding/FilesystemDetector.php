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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\PathEncoding;

use Sjorek\RuntimeCapability\Detection\DefaultCharsetDetector;
use Sjorek\RuntimeCapability\Detection\LocaleCharsetDetector;
use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDependingDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\PathEncodingDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Target\FileTargetInterface;
use Sjorek\RuntimeCapability\Utility\CharsetUtility;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDependingDetector implements PathEncodingDetectorInterface
{
    /**
     * @var string[]
     */
    const FILESYSTEM_DRIVER_CONFIG_TYPES = [
        'subclass:' . FilesystemDriverInterface::class,
        'subclass:' . FileTargetInterface::class,
    ];

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
        'filesystem-driver' => FileTargetDriver::class,
        'filepath-encoding' => 'BINARY',
        'filename-tests' => self::UTF8_FILENAME_TESTS,
        'detection-target-pattern' => self::DETECTION_TARGET_PATTERN,
    ];

    /**
     * @var FileTargetInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $filepathEncoding = 'BINARY';

    /**
     * @var bool[]|string[]
     */
    protected $filenameTests = [];

    /**
     * @var string
     */
    protected $detectionTargetPattern = '%s-%s';

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Detection\AbstractDependingDetector::depends()
     */
    public function depends()
    {
        return $this->filesystemDriver instanceof PHPFilesystemDriverInterface ? static::DEPENDENCIES : [];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Detection\AbstractDetector::setup()
     */
    public function setup()
    {
        parent::setup();

        $filepathEncoding = $this->config('filepath-encoding', 'string');
        if ('BINARY' !== $filepathEncoding &&
            null == ($filepathEncoding = CharsetUtility::normalizeEncodingName($filepathEncoding))) {
            throw new ConfigurationFailure(
                sprintf('Invalid configuration value for key "filesystem-encoding": %s', $filepathEncoding),
                1521291497
            );
        }
        $this->filepathEncoding = $filepathEncoding;

        $this->filenameTests = $this->config('filename-tests', 'array');

        $this->detectionTargetPattern =
            $this->config(
                'detection-target-pattern',
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
     * php >     'UTF-8' => [
     * php >         NormalizationForms::NONE => true,
     * php >         NormalizationForms::NFC => true,
     * php >         NormalizationForms::NFD => true,
     * php >         NormalizationForms::NFKC => true,
     * php >         NormalizationForms::NFKC => true,
     * php >         NormalizationForms::NFD_MAC => false,
     * php >     ]
     * php > ]
     * </pre>
     *
     * {@inheritdoc}
     *
     * @param array  $localeCharset
     * @param string $defaultCharset
     *
     * @return array[]|bool[]|bool[]
     *
     * @see \Sjorek\RuntimeCapability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluateWithDependency(array $localeCharset = null, string $defaultCharset = null)
    {
        $charset = $this->filepathEncoding;
        if ('BINARY' === $charset ||
            (empty($this->depends()) && null === $localeCharset && null === $defaultCharset) ||
            $charset === $localeCharset[LC_CTYPE] ||
            $charset === $defaultCharset) {
            return [
                $charset => $this->testFilesystem(array_map(function () { return null; }, $this->filenameTests)),
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
                if ($this->filesystemDriver->createTarget($fileName)) {
                    $tests[$index] = $this->filesystemDriver->existsTarget($fileName);
                    $this->filesystemDriver->removeTarget($fileName);
                }
            } catch (\Exception $e) {
                $tests[$index] = false;
            }
        }

        return $tests;
    }

    /**
     * @param int    $index
     * @param string $testString
     *
     * @return string
     */
    protected function generateDetectionFileNameForIndex($index, $testString)
    {
        $fileName = sprintf($this->detectionTargetPattern, $index, $testString);
        if (in_array($this->filepathEncoding, [null, 'BINARY', 'UTF-8'], true)) {
            return $fileName;
        }

        return mb_convert_encoding($fileName, $this->filesystemPathEncoding, 'UTF-8');
    }
}
