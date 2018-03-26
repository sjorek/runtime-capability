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
interface IdentifiableInterface
{
    /**
     * Used to extract 'Class' part from '\OptionalNamespace\ClassNames'.
     *
     * @var string
     */
    const EXTRACT_ID_PATTERN =
        '/
        ^                                       # start with
        (?:.*\\\\)*                             # do not capture zero or more repetitions of anything followed by
                                                # a backslash; followed by
        (?:                                     # not capturing group of
            ([A-Z][A-Za-z0-9]+)                 # capturing an upper-case letter followed by
                                                # one or more letters or digits; followed by
            [A-Z][A-Za-z0-9]+                   # an upper-case letter followed by one or more letters or digits
            |                                   # or
            ([A-Z][A-Za-z0-9]+)                 # capturing an upper-case letter followed by
                                                # one or more letters or digits
        )                                       # end of not capturing group
        $                                       # up to the end
        /x'
    ;

    /**
     * Used to normalize CamelCase to to dash-case, with support for ABBReviations.
     *
     * @var string
     */
    const NORMALIZE_ID_PATTERN =
        '/
        [A-Z]+[0-9]*$                           # match one or more upper-case letters, followed by zero or
                                                # more digits, at the end
        |                                       # or
        (?<=[A-Za-z0-9])[A-Z](?=[a-z])          # match an upper-case letter, preceded by a letter or a digit
                                                # and followed by a lower-case letter
        |                                       # or
        (?<=[a-z0-9])[A-Z](?=[A-Za-z])          # match an upper-case letter, preceded by a lower-case letter
                                                # or a digit and followed by a letter
        /x'
    ;

    /**
     * @return string
     */
    public function identify(): string;
}
