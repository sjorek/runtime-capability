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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver\PHP\Target;

use Sjorek\RuntimeCapability\Filesystem\Driver\FileTargetDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\FileTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * FileTargetDirectoryDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDirectoryDriver
 */
class FileTargetDirectoryDriverTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $driver = new FileTargetDirectoryDriver();
        $this->assertInstanceOf(FileTargetDirectoryDriverInterface::class, $driver);
        $this->assertAttributeInstanceOf(FileTargetDriverInterface::class, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(FileTargetDriver::class, 'targetDriver', $driver);
    }
}
