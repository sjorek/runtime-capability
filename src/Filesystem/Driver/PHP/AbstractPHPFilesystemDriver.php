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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver;
use Sjorek\RuntimeCapability\Utility\FilesystemUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractPHPFilesystemDriver extends AbstractFilesystemDriver implements PHPFilesystemDriverInterface
{
    /**
     * @var string
     */
    protected $workingDirectory = null;

    /**
     * Normalize the given path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  CWD             # replace single dot with current working directory
     * ./test       =>  CWD/test        # replace leading dot with current working directory in path
     * .\test       =>  CWD/test        # replace leading dot with current working directory in windows path
     * test/file    =>  CWD/test/file   # prepend current working directory to relative path
     * test\file    =>  CWD/test/file   # prepend current working directory to relative windows path
     * /test        =>  /test           # keep absolute posix path absolute
     * c:\test      =>  c:/test         # keep absolute windows path absolute
     * </pre>
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path): string
    {

        $this->validatePath($path);

        $path = FilesystemUtility::normalizePath($path);

        if (!(FilesystemUtility::isAbsolutePath($path) || FilesystemUtility::isUrl($path))) {
            if ('' === $path) {
                $path = $this->getWorkingDirectory();
            } else {
                $path = $this->getWorkingDirectory() . '/' . $path;
            }
        }

        $this->validatePath($path);

        return $path;
    }

    protected function setWorkingDirectory(string $path): string
    {
        return $this->workingDirectory = $this->normalizePath($path);
    }

    /**
     * Return the current working directory.
     *
     * @return string
     *
     * @see FilesystemUtility::getWorkingDirectory()
     */
    protected function getWorkingDirectory(): string
    {
        if (null !== $this->workingDirectory) {
            return $this->workingDirectory;
        }

        return $this->workingDirectory = FilesystemUtility::getCurrentWorkingDirectory();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if the path is an url
     *
     * @see AbstractFilesystemDriver::validatePath()
     */
    protected function validatePath(string $path): bool
    {
        if (!FilesystemUtility::isLocalPath($path)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid path given: %s. The driver supports local paths and urls with file- or vfs-scheme only.',
                    $path
                ),
                1522171543
            );
        }

        return parent::validatePath($path);
    }
}
