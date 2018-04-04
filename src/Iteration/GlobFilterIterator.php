<?php
namespace Sjorek\RuntimeCapability\Iteration;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class GlobFilterIterator extends \FilterIterator
{
    /**
     * @var int
     */
    const DEFAULT_FLAGS =
        FNM_PATHNAME |
        FNM_PERIOD |
        FNM_CASEFOLD
    ;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $flags;

    /**
     * @param \Iterator $iterator
     * @param string $pattern
     * @param int $flags
     */
    public function __construct(\Iterator $iterator, string $pattern, int $flags = self::DEFAULT_FLAGS)
    {
        $this->pattern = $pattern;
        $this->flags = $flags;

        parent::__construct($iterator);
    }

    /**
     * {@inheritDoc}
     * @see \FilterIterator::accept()
     */
    public function accept()
    {
        return fnmatch($this->pattern, $this->current(), $this->flags);
    }
}