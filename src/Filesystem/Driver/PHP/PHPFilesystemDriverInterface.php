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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Strategy\FilesystemStrategyInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface PHPFilesystemDriverInterface extends FilesystemDriverInterface, FilesystemStrategyInterface
{
}
