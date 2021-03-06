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

use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManager;
use Sjorek\RuntimeCapability\Filesystem\Driver\FilesystemDriverManagerInterface;
use Sjorek\RuntimeCapability\Management\ManagerInterface;
use Sjorek\RuntimeCapability\Tests\Fixtures\Filesystem\Driver\FilesystemDriverTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * AbstractFilesystemDriver test case.
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
     * @covers ::setManager
     * @covers ::setFilesystemDriverManager
     */
    public function testSetManager(): FilesystemDriverTestFixture
    {
        $actual = $this->subject;
        $expect = new FilesystemDriverManager();
        $this->assertSame($this->subject, $actual->setManager($expect));
        $this->assertAttributeSame($expect, 'manager', $actual);
        $this->assertAttributeInstanceOf(ManagerInterface::class, 'manager', $actual);
        $this->assertAttributeInstanceOf(FilesystemDriverManagerInterface::class, 'manager', $actual);

        return $this->subject;
    }

    /**
     * @covers ::getManager
     * @covers ::getFilesystemDriverManager
     * @depends testSetManager
     */
    public function testGetManager(FilesystemDriverTestFixture $actual)
    {
        $this->assertAttributeSame($actual->getManager(), 'manager', $actual);
        $this->assertInstanceOf(ManagerInterface::class, $actual->getManager());
        $this->assertInstanceOf(FilesystemDriverManagerInterface::class, $actual->getManager());
    }

    /**
     * @covers ::getMaximumPathLength
     *
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
     *
     * @return string
     */
    public function testHasValidPathLength(int $maximumPathLength): string
    {
        $validPath = str_pad('', $maximumPathLength, 'x');
        $invalidPath1 = $validPath . 'x';
        $invalidPath2 = '';

        $this->assertTrue($this->callProtectedMethod($this->subject, 'hasValidPathLength', $validPath));
        $this->assertFalse($this->callProtectedMethod($this->subject, 'hasValidPathLength', $invalidPath1));
        $this->assertFalse($this->callProtectedMethod($this->subject, 'hasValidPathLength', $invalidPath2));

        return $validPath;
    }

    /**
     * @covers ::validatePath
     * @testWith ["."]
     *           ["./"]
     *           ["/"]
     *           ["c:/"]
     *           ["c:/test"]
     *           ["c:/test/"]
     *           ["c:\\"]
     *           ["c:\\test"]
     *           ["c:\\test\\"]
     *           ["test"]
     *           ["test/"]
     *           ["/test/test2"]
     *           ["/test/test2/"]
     */
    public function testValidatePath(string $path)
    {
        $this->assertTrue($this->callProtectedMethod($this->subject, 'validatePath', $path));
    }

    /**
     * @covers ::validatePath
     */
    public function testValidatePathWithEmptyPathThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path given. The path is empty ("").');
        $this->expectExceptionCode(1522171135);
        $this->callProtectedMethod($this->subject, 'validatePath', '');
    }

    /**
     * @covers ::validatePath
     * @depends testGetMaximumPathLength
     * @depends testHasValidPathLength
     *
     * @param int    $length
     * @param string $path
     */
    public function testValidatePathWithExceededMaximumPathLengthThrowsException(int $length, string $path)
    {
        $path .= 'x';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid path given: %s. The path exceeds the maximum path length of %s bytes.',
                $path,
                $length
            )
        );
        $this->expectExceptionCode(1522171138);
        $this->callProtectedMethod($this->subject, 'validatePath', $path);
    }

    /**
     * @covers ::validatePath
     * @testWith [".."]
     *           ["./.."]
     *           ["/../"]
     *           ["c:/.."]
     *           ["c:/../test"]
     *           ["c:\\.."]
     *           ["c:\\..\\test"]
     *           ["test/.."]
     *           ["test/../test"]
     */
    public function testValidatePathWithPathTraversalThrowsException(string $path): string
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Invalid path given: %s. Path traversal ("..") is not allowed.', $path)
        );
        $this->expectExceptionCode(1522171140);
        $this->callProtectedMethod($this->subject, 'validatePath', $path);
    }
}
