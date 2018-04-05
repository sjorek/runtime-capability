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

namespace Sjorek\RuntimeCapability\Unit\Iteration;

use Sjorek\RuntimeCapability\Iteration\GlobFilterKeyIterator;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * GlobFilterKeyIterator test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Iteration\GlobFilterKeyIterator
 */
class GlobFilterKeyIteratorTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     *
     * @return GlobFilterKeyIterator
     */
    public function testConstruct(): GlobFilterKeyIterator
    {
        $pattern = 'b*';
        $iterator = new GlobFilterKeyIterator(
            new \ArrayIterator(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']),
            $pattern
        );

        $this->assertAttributeSame($pattern, 'pattern', $iterator);
        $this->assertAttributeSame(GlobFilterKeyIterator::DEFAULT_FLAGS, 'flags', $iterator);

        return $iterator;
    }

    /**
     * @covers ::accept
     * @depends testConstruct
     *
     * @param GlobFilterKeyIterator $iterator
     */
    public function testAccept(GlobFilterKeyIterator $iterator)
    {
        $this->assertSame(['bar' => 'bar', 'baz' => 'baz'], iterator_to_array($iterator, true));
    }
}
