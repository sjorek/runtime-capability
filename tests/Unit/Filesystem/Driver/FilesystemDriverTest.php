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

namespace Sjorek\RuntimeCapability\Tests\Unit\Filesystem\Driver;

use Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\FilesystemDriverTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver
 */
class FilesystemDriverTest extends AbstractTestCase
{
    /**
     * @var \Sjorek\RuntimeCapability\Filesystem\Driver\AbstractFilesystemDriver
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subject = new FilesystemDriverTestFixture();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        parent::tearDown();
    }

    /**
     * @covers ::getMaximumPathLength
     * @return int
     */
    public function testGetMaximumPathLength(): int
    {
        $actual = $this->subject->getMaximumPathLength();
        $this->assertInternalType('integer', $actual);
        $this->assertGreaterThan(0, $actual);
        $this->assertLessThanOrEqual(PHP_MAXPATHLEN, $actual);

        return $actual;
    }

    /**
     * @covers ::hasValidPathLength
     * @depends testGetMaximumPathLength
     *
     * @param int $maximumPathLength
     */
    public function testHasValidPathLength(int $maximumPathLength)
    {
        $validPath = str_pad('', $maximumPathLength, 'x');
        $invalidPath1 = $validPath . 'x';
        $invalidPath2 = '';

        $this->assertTrue($this->callProtectedMethod($this->subject, 'hasValidPathLength', $validPath));
        $this->assertFalse($this->callProtectedMethod($this->subject, 'hasValidPathLength', $invalidPath1));
        $this->assertFalse($this->callProtectedMethod($this->subject, 'hasValidPathLength', $invalidPath2));
    }
}
