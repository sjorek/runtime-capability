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
     * Normalize the given path any validate the normalized path.
     *
     * Hint: CWD = getcwd();
     *
     * <pre>
     * .            =>  EMPTY       # replace single dot with current working directory
     * ./test       =>  test        # replace leading dot with current working directory in path
     * .\test       =>  test        # replace leading dot with current working directory in windows path
     * test/file    =>  test/file   # prepend current working directory to relative path
     * test\file    =>  test/file   # prepend current working directory to relative windows path
     * </pre>
     *
     * @param string $path
     *
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        $path = FilesystemUtility::normalizePath($path);

        $this->validatePath($path);

        return $path;
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
