<?php
namespace Sjorek\RuntimeCapability\Iteration;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class GlobFilterKeyIterator extends GlobFilterIterator
{
    /**
     * {@inheritDoc}
     * @see GlobFilterIterator::accept()
     */
    public function accept()
    {
        return fnmatch($this->pattern, $this->key(), $this->flags);
    }
}