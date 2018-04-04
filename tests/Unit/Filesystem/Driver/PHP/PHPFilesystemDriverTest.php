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
use Sjorek\RuntimeCapability\Tests\Unit\AbstractFilesystemTestCase;

/**
 * Identifiable test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Filesystem\Driver\PHP\AbstractPHPFilesystemDriver
 */
class PHPFilesystemDriverTest extends AbstractFilesystemTestCase
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

    /**
     * @covers ::normalizePath
     * @dataProvider provideTestNormalizePathData
     *
     * @param string $expect
     * @param string $input
     */
    public function testNormalizePath(string $expect, string $input)
    {
        if (0 === strpos($input, 'c:')) {
            $ns = $this->getFilesystemUtilityNamespace();
            $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        }
        $this->assertSame($expect, $this->callProtectedMethod($this->subject, 'normalizePath', $input));
        if (0 === strpos($input, 'c:')) {
            unset($GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR']);
        }
    }

    /**
     * @return string[][]
     */
    public function provideTestNormalizePathData()
    {
        $cwd = getcwd();

        return array_map(
            function ($value) use ($cwd) {
                $value[0] = preg_replace('/^CWD/', $cwd, $value[0]);

                return $value;
            },
            $this->extractTestDataFromDocComment(
               (new \ReflectionMethod(PHPFilesystemDriverTestFixture::class, 'normalizePath'))->getDocComment()
            )
        );
    }

    /**
     * @covers ::setWorkingDirectory
     * @dataProvider provideTestSetWorkingDirectoryData
     *
     * @param string $expect
     * @param string $input
     */
    public function testSetWorkingDirectory(string $expect, string $input)
    {
        if (0 === strpos($input, 'c:')) {
            $ns = $this->getFilesystemUtilityNamespace();
            $GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR'] = '\\';
        }
        $this->assertSame($expect, $this->callProtectedMethod($this->subject, 'setWorkingDirectory', $input));
        $this->assertAttributeSame($expect, 'workingDirectory', $this->subject);
        if (0 === strpos($input, 'c:')) {
            unset($GLOBALS[$ns]['constant']['DIRECTORY_SEPARATOR']);
        }
    }

    /**
     * @return string[][]
     */
    public function provideTestSetWorkingDirectoryData()
    {
        return $this->provideTestNormalizePathData();
    }

    /**
     * @covers ::getWorkingDirectory
     */
    public function testGetWorkingDirectory()
    {
        $this->assertSame(getcwd(), $this->callProtectedMethod($this->subject, 'getWorkingDirectory'));
        $this->assertAttributeSame(
            // call twice for whole coverage !
            $this->callProtectedMethod($this->subject, 'getWorkingDirectory'), 'workingDirectory', $this->subject
        );
    }
}
