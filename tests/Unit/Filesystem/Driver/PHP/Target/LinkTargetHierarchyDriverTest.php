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

use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\LinkTargetHierarchyDriverInterface;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetHierarchyDriver;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * LinkTargetHierarchyDriver test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\LinkTargetHierarchyDriver
 */
class LinkTargetHierarchyDriverTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $driver = new LinkTargetHierarchyDriver();
        $this->assertInstanceOf(LinkTargetHierarchyDriverInterface::class, $driver);
        $this->assertAttributeInstanceOf(LinkTargetDriverInterface::class, 'targetDriver', $driver);
        $this->assertAttributeInstanceOf(LinkTargetDriver::class, 'targetDriver', $driver);
    }
}
