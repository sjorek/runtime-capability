<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalization;

use Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalizationDetectorInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FlatFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\PHP\FlatFilesystemDriver;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FlatFilesystemDetector extends FilesystemDetector implements PathNormalizationDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FlatFilesystemDriver::class,
        'filesystem-path' => '.',
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var FlatFilesystemDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $filesystemPath;

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDetector::setup()
     */
    public function setup(array &$configuration)
    {
        parent::setup($configuration);
        $this->filesystemPath = $this->getConfiguration('filesystem-path', 'string');
    }

    /**
     * Detect utf8-capabilities for given path.
     *
     * The result will look like one of the following examples.
     *
     * Example 1: Filesystem has no utf8-capabilities at all
     * <pre>
     * php > false
     * </pre>
     *
     * Example 2: Filesystem has utf8-capabilities, but does not normalize anything (~ treats paths as binary)
     * <pre>
     * php > true
     * </pre>
     *
     * Example 4: Filesystem has utf8-capabilities and fails with some normalizations for read and write
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
     * Example 5: Filesystem has utf8-capabilities and normalizes sometimes on write, but not on read
     * <pre>
     * php > [
     * php >      NormalizationForms::NONE => false,
     * php >      NormalizationForms::NFC => [
     * php >          'read' => false,
     * php >          'write' => true,
     * php >      ],
     * php >      NormalizationForms::NFD => [
     * php >          'read' => false,
     * php >          'write' => true,
     * php >      ],
     * php >      NormalizationForms::NFKC => [
     * php >          'read' => false,
     * php >          'write' => false,
     * php >      ],,
     * php >      NormalizationForms::NFKC => [
     * php >          'read' => false,
     * php >          'write' => false,
     * php >      ],,
     * php >      NormalizationForms::NFD_MAC => [
     * php >          'read' => false,
     * php >          'write' => true,
     * php >      ]
     * php > ]
     * </pre>
     *
     * @param array $normalizations
     * @param array $tests
     */
    protected function testFilesystem(array &$normalizations, array $tests)
    {
        $this->filesystemDriver->setPath($this->filesystemPath);
        $fileNames = [];
        foreach ($tests as $normalization => $fileName) {
            if (false === $fileName) {
                continue;
            }
            $normalizations[$normalization] = [
                'read' => false,
                'write' => true,
            ];
            $fileName = sprintf($this->filenameDetectionPattern, $normalization, hex2bin($fileName));
            $fileNames[$normalization] = $fileName;
            try {
                $this->filesystemDriver->create($fileName);
            } catch (IOExceptionInterface $e) {
                $normalizations[$normalization]['write'] = false;
            }
        }
        $this->testFilesystemRead($normalizations, $tests, $fileNames);
    }

    /**
     * @param array $normalizations
     * @param array $tests
     * @param array $fileNames
     */
    protected function testFilesystemRead(array &$normalizations, array $tests, array $fileNames)
    {
        foreach ($this->filesystemDriver as $fileName) {
            foreach ($fileNames as $normalization => $candidate) {
                if ($normalizations[$normalization]['read'] === true) {
                    continue;
                }
                // If all files exist then the filesystem does not normalize unicode. If
                // some files are missing then the filesystem either normalizes unicode
                // or it denies access to not-normalized paths or it simply does not support
                // unicode at all, at least not those normalization forms we test.
                if ($fileName === $candidate) {
                    $normalizations[$normalization]['read'] = true;
                }
            }
            $this->filesystemDriver->remove($fileName);
        }
    }

    /**
     * @param FlatFilesystemDriverInterface $driver
     *
     * @return PathNormalizationDetectorInterface
     */
    protected function setFilesystemDriver(FlatFilesystemDriverInterface $driver): PathNormalizationDetectorInterface
    {
        $this->filesystemDriver = $driver;

        return $this;
    }
}
