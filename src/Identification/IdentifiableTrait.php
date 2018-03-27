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

namespace Sjorek\RuntimeCapability\Identification;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait IdentifiableTrait
{
    /**
     * @var string
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @see IdentifiableInterface::identify()
     */
    public function identify(): string
    {
        if (null !== $this->id) {
            return $this->id;
        }
        $className = static::class;
        if (defined($className . '::IDENTIFIER')) {
            return $this->id = (string) static::IDENTIFIER;
        }
        $id = $this->extractIdentifier($className);

        return $this->id = $id === $className ? $id : $this->normalizeIdentifier($id);
    }

    /**
     * Extract identifier from given classname (optionally preceded by a namespace).
     *
     * <pre>
     * Test1            => Test1            # keep whole classname, if there are not camel-case sections
     * Test1Test2       => Test1            # strip last camel-case section from classname with two sections
     * Test1Test2Test3  => Test1Test2       # strip last camel-case section from classname with multiple sections
     * Test1\Test2      => Test2            # strip namespace from classname
     * Test1\Test2Test3 => Test2            # strip namespace and last camel-case section from classname
     * anything-else    => anything-else    # keep anything not representing a classname as it is
     * </pre>
     *
     * @param string $className
     *
     * @return string
     */
    protected function extractIdentifier($className)
    {
        return preg_replace(static::EXTRACT_ID_PATTERN, '$1$2', $className);
    }

    /**
     * Normalize given identifier.
     *
     * <pre>
     * test1            => test1            # lower-case stays lower-case
     * Test1            => test1            # upper-case is converted to lower-case
     * test1Test2       => test1-test2      # camel-case with initial lower-case letter
     * Test1Test2       => test1-test2      # camel-case with initial upper-case letter
     * test1PHP         => test1-php        # camel-case ending with upper-case abbreviation
     * Test1PHP7        => test1-php7       # camel-case ending with upper-case abbreviation plus digit
     * PHPTest1         => php-test1        # camel-case starting with upper-case abbreviation
     * PHP7Test1        => php7-test1       # camel-case starting with upper-case abbreviation plus digit
     * test1PHPTest2    => test1-php-test2  # camel-case surrounding an upper-case abbreviation
     * Test1PHP7Test2   => test1-php7-test2 # camel-case surrounding an upper-case abbreviation plus digit
     * no-camel-case    => no-camel-case    # keep anything not using any camel-case as it is
     * </pre>
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function normalizeIdentifier($identifier)
    {
        return strtolower(preg_replace(static::NORMALIZE_ID_PATTERN, '-$0', $identifier));
    }
}
