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

use Sjorek\RuntimeCapability\Filesystem\Driver\FlatFilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\FlatFilesystemDriver;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FlatFilesystemDetector extends FilesystemDetector
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
    public function setup()
    {
        parent::setup();
        $this->filesystemPath = $this->config('filesystem-path', 'match:^\.?(?:[^.]|\.[^.])*$');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see FilesystemDetector::evaluate()
     */
    protected function evaluate()
    {
        $this->filesystemDriver->setPath($this->filesystemPath);

        return parent::evaluate();
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
     * {@inheritdoc}
     *
     * @see FilesystemDetector::testFilesystem()
     */
    protected function testFilesystem(array $normalizations, array $tests): array
    {
        $fileNames = [];
        foreach ($tests as $form => $fileName) {
            if (false === $fileName || !isset($normalizations[$form])) {
                continue;
            }
            $normalizations[$form] = [
                'read' => false,
                'write' => true,
            ];
            $fileName = sprintf($this->filenameDetectionPattern, $form, hex2bin($fileName));
            $fileNames[$form] = $fileName;
            try {
                $this->filesystemDriver->create($fileName);
            } catch (IOExceptionInterface $e) {
                $normalizations[$form]['write'] = false;
            }
        }
        foreach ($this->filesystemDriver as $fileName) {
            foreach ($fileNames as $form => $candidate) {
                if ($normalizations[$form]['read'] === true) {
                    continue;
                }
                // If all files exist then the filesystem does not normalize unicode. If
                // some files are missing then the filesystem either normalizes unicode
                // or it denies access to not-normalized paths or it simply does not support
                // unicode at all, at least not those normalization forms we test.
                if ($fileName === $candidate) {
                    $normalizations[$form]['read'] = true;
                }
            }
            $this->filesystemDriver->remove($fileName);
        }

        return $normalizations;
    }

    /**
     * @return FlatFilesystemDriverInterface
     */
    protected function setupFilesystemDriver(): FlatFilesystemDriverInterface
    {
        return $this->manager->getManagement()->getFilesystemDriverManager(
            $this->config('filesystem-driver', 'subclass:' . FlatFilesystemDriverInterface::class)
        );
    }
}
