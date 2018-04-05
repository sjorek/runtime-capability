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

namespace Sjorek\RuntimeCapability\Iteration;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class GlobFilterKeyIterator extends GlobFilterIterator
{
    /**
     * {@inheritdoc}
     *
     * @see GlobFilterIterator::accept()
     */
    public function accept()
    {
        return fnmatch($this->pattern, $this->key(), $this->flags);
    }
}
