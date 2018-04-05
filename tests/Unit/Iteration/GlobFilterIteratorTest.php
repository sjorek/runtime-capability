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

use Sjorek\RuntimeCapability\Iteration\GlobFilterIterator;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * GlobFilterIterator test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Iteration\GlobFilterIterator
 */
class GlobFilterIteratorTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     *
     * @return GlobFilterIterator
     */
    public function testConstruct(): GlobFilterIterator
    {
        $pattern = 'b*';
        $iterator = new GlobFilterIterator(
            new \ArrayIterator(['foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz']),
            $pattern
        );

        $this->assertAttributeSame($pattern, 'pattern', $iterator);
        $this->assertAttributeSame(GlobFilterIterator::DEFAULT_FLAGS, 'flags', $iterator);

        return $iterator;
    }

    /**
     * @covers ::accept
     * @depends testConstruct
     *
     * @param GlobFilterIterator $iterator
     */
    public function testAccept(GlobFilterIterator $iterator)
    {
        $this->assertSame(['bar' => 'bar', 'baz' => 'baz'], iterator_to_array($iterator, true));
    }
}
