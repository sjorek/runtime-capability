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

namespace Sjorek\RuntimeCapability\Filesystem\Detection\Encoding\Utf8;

use Sjorek\RuntimeCapability\Filesystem\Detection\AbstractFilesystemDetector;
use Sjorek\RuntimeCapability\Filesystem\Detection\Encoding\Utf8EncodingDetectorInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FilesystemDriver;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractFilesystemDetector implements Utf8EncodingDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriver::class,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var string
     */
    protected $filenameDetectionPattern;

    /**
     * {@inheritdoc}
     *
     * @see AbstractFilesystemDetector::setup()
     */
    public function setup()
    {
        parent::setup();
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
     * @see \Sjorek\RuntimeCapability\Capability\Detection\AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return $this->testFilesystem(
            array_map(function () { return null; }, self::FILENAME_TESTS),
            self::FILENAME_TESTS
        );
    }

    /**
     * @param array $normalizations
     * @param array $tests
     *
     * @return array
     */
    protected function testFilesystem(array $normalizations, array $tests): array
    {
        foreach ($tests as $form => $fileName) {
            if (false === $fileName || !isset($normalizations[$form])) {
                continue;
            }
            $fileName = sprintf($this->filenameDetectionPattern, $form, hex2bin($fileName));
            try {
                $normalizations[$form] =
                    $this->filesystemDriver->create($fileName) &&
                    $this->filesystemDriver->exists($fileName) &&
                    $this->filesystemDriver->remove($fileName)
                ;
            } catch (IOExceptionInterface $e) {
                $normalizations[$form] = false;
            }
        }

        return $normalizations;
    }
}
