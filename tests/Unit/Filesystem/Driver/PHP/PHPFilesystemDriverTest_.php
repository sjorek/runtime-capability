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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\PHP\PHPFilesystemDriverTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver
 */
class PHPFilesystemDriverTest_ extends AbstractTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new PHPFilesystemDriverTestFixture();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        parent::tearDown();
    }
}
