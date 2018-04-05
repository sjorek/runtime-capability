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
     * @param string    $pattern
     * @param int       $flags
     */
    public function __construct(\Iterator $iterator, string $pattern, int $flags = self::DEFAULT_FLAGS)
    {
        $this->pattern = $pattern;
        $this->flags = $flags;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     *
     * @see \FilterIterator::accept()
     */
    public function accept()
    {
        return fnmatch($this->pattern, $this->current(), $this->flags);
    }
}
