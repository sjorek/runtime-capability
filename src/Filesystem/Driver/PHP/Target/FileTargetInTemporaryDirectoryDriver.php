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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\PHPTemporaryDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Target\FileTargetInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class FileTargetInTemporaryDirectoryDriver extends PHPTemporaryDirectoryDriver implements FileTargetInterface
{
    public function __construct()
    {
        parent::__construct(new FileTargetDriver());
    }
}
