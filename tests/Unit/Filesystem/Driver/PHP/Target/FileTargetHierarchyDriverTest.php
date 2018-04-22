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

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetHierarchyDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\Target\FileTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\Target\FileTargetHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * FileTargetHierarchyDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetHierarchyDriver
 */
class FileTargetHierarchyDriverTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $driver = new FileTargetHierarchyDriver();
        $this->assertInstanceOf(FileTargetHierarchyDriverInterface::class, $driver);
        $this->assertAttributeInstanceOf(FileTargetDriverInterface::class, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(FileTargetDriver::class, 'targetDriver', $driver);
    }
}
