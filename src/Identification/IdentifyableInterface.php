<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
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
interface IdentifyableInterface
{
    /**
     * Used to extract 'Class' part from '\OptionalNamespace\ClassNames'.
     *
     * @var string
     */
    const EXTRACT_ID_PATTERN =
        '/
        ^                                  # start with
        (?:.*[\\\\])*                      # do not capture zero or more repetitions of anything followed by
                                           # a backslash; followed by
        ([A-Z][A-Za-z0-9]+)                # capturing an upper-case letter followed by
                                           # one or more letters and/or digits; followed by
        (?:[A-Z][a-z0-9]+)                 # do not capture an upper-case letter followed by
                                           # one or more lower-case letters and/or digits
        $                                  # up to the end
        /x'
    ;

    /**
     * Used to normalize CamelCase to to dash-case, with support for ABBReviations.
     *
     * <pre>
     * test1               => test1
     * Test1               => test1
     * test1Test2          => test1-test2
     * Test1Test2          => test1-test2
     * test1PHP            => test1-php
     * Test1PHP7           => test1-php7
     * PHPTest1            => php-test1
     * PHP7Test1           => php7-test1
     * test1PHPTest2       => test1-php-test2
     * Test1PHP7Test2      => test1-php7-test2
     * </pre>
     *
     * @var string
     */
    const NORMALIZE_ID_PATTERN =
        '/
        [A-Z]+[0-9]*$                       # match one or more upper-case letters, followed by zero or
                                            # more digits, at the end
        |                                   # or
        (?<=[A-Za-z0-9])[A-Z](?=[a-z0-9])   # match an upper-case letter, preceded by a letter or a digit
                                            # and followed by a lower-case letter and/or a digit
        |                                   # or
        (?<=[a-z0-9])[A-Z](?=[A-Za-z0-9])   # match an upper-case letter, preceded by a lower-case letter
                                            # and/or a digit and followed by a letter or a digit
        /x'
    ;

    /**
     * @return string
     */
    public function identify(): string;
}
