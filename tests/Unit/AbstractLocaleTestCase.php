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

namespace Sjorek\RuntimeCapability\Tests\Unit;

use Sjorek\RuntimeCapability\Utility\CharsetUtility;

/**
 * Filesystem test case.
 */
abstract class AbstractLocaleTestCase extends AbstractTestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        unset($GLOBALS[$this->getCharsetUtilityNamespace()]);

        require_once str_replace(
            ['/Unit/', 'AbstractLocaleTestCase.php'],
            ['/Fixtures/', 'LocaleTestFixture.php'],
            __FILE__
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($GLOBALS[$this->getCharsetUtilityNamespace()]);

        parent::tearDown();
    }

    // ////////////////////////////////////////////////////////////////
    // utility methods
    // ////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    protected function getCharsetUtilityNamespace(): string
    {
        return implode('\\', array_slice(explode('\\', CharsetUtility::class), 0, -1));
    }
}
