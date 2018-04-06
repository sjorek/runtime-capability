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

use Sjorek\RuntimeCapability\Filesystem\Driver\DirectoryTargetDirectoryDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\DirectoryTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDirectoryDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * DirectoryTargetDirectoryDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\DirectoryTargetDirectoryDriver
 */
class DirectoryTargetDirectoryDriverTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $driver = new DirectoryTargetDirectoryDriver();
        $this->assertInstanceOf(DirectoryTargetDirectoryDriverInterface::class, $driver);
        $this->assertAttributeInstanceOf(DirectoryTargetDriverInterface::class, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(DirectoryTargetDriver::class, 'targetDriver', $driver);
    }
}
