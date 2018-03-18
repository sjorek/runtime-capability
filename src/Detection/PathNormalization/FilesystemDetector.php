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

use Sjorek\RuntimeCapability\Capability\Detection\AbstractDetector;
use Sjorek\RuntimeCapability\Capability\Filesystem\Detection\PathNormalizationDetectorInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\FilesystemDriverManagerInterface;
use Sjorek\RuntimeCapability\Capability\Filesystem\Driver\PHP\FilesystemDriver;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class to detect unicode filesystem capabilities.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FilesystemDetector extends AbstractDetector implements PathNormalizationDetectorInterface
{
    /**
     * @var int[]
     */
    protected static $DEFAULT_CONFIGURATION = [
        'filesystem-driver' => FilesystemDriver::class,
        'filename-detection-pattern' => self::DETECTION_FILENAME_PATTERN,
    ];

    /**
     * @var FilesystemDriverManagerInterface
     */
    protected $filesystemDriverManager;

    /**
     * @var FilesystemDriverInterface
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $filenameDetectionPattern;

    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::setup()
     */
    public function setup(array &$configuration)
    {
        parent::setup($configuration);
        $this->setFilesystemDriver(
            $this->filesystemDriverManager->get($this->getConfiguration('filesystem-driver', 'string'))
        );
        $this->filenameDetectionPattern = $this->getConfiguration('filename-detection-pattern', 'string');
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
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        $normalizations = array_map(function () { return false; }, self::FILENAME_TESTS);
        $this->testFilesystem($normalizations, self::FILENAME_TESTS);

        return $normalizations;
    }

    /**
     * @param array $normalizations
     * @param array $tests
     */
    protected function testFilesystem(array &$normalizations, array $tests)
    {
        foreach ($tests as $normalization => $fileName) {
            if (false === $fileName) {
                continue;
            }
            $fileName = sprintf($this->filenameDetectionPattern, $normalization, hex2bin($fileName));
            try {
                $normalizations[$normalization] =
                    $this->filesystemDriver->create($fileName) &&
                    $this->filesystemDriver->exists($fileName) &&
                    $this->filesystemDriver->remove($fileName)
                ;
            } catch (IOExceptionInterface $e) {
                $normalizations[$normalization] = false;
            }
        }
    }

    /**
     * @param FilesystemDriverInterface $driver
     *
     * @return PathNormalizationDetectorInterface
     */
    protected function setFilesystemDriver(FilesystemDriverInterface $driver): PathNormalizationDetectorInterface
    {
        $this->filesystemDriver = $driver;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see PathNormalizationDetectorInterface::setFilesystemDriverManager()
     */
    public function setFilesystemDriverManager(FilesystemDriverManagerInterface $manager): PathNormalizationDetectorInterface
    {
        $this->filesystemDriverManager = $manager;

        return $this;
    }
}
