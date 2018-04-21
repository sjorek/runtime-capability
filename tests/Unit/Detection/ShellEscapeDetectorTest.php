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

namespace Sjorek\RuntimeCapability\Tests\Unit\Detection;

use Sjorek\RuntimeCapability\Exception\ConfigurationFailure;
use Sjorek\RuntimeCapability\Detection\ShellEscapeDetector;
use Sjorek\RuntimeCapability\Tests\Fixtures\Configuration\ConfigurationTestFixture;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;

/**
 * ShellEscapeDetector test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Detection\ShellEscapeDetector
 */
class ShellEscapeDetectorTest extends AbstractTestCase
{
    /**
     * @var ShellEscapeDetector
     */
    private $subject;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        unset($GLOBALS['Sjorek\\RuntimeCapability\\Detection']);

        require_once str_replace(
            ['/Unit/', '.php'],
            ['/Fixtures/', 'Fixture.php'],
            __FILE__
        );

        $this->subject = (new ShellEscapeDetector())->setConfiguration(new ConfigurationTestFixture());
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->subject = null;

        unset($GLOBALS['Sjorek\\RuntimeCapability\\Detection']);

        parent::tearDown();
    }

    /**
     * @covers ::setup
     */
    public function testSetup()
    {
        $config = new ConfigurationTestFixture(['compact-result' => true, 'charset' => 'utf8']);
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame(false, 'compactResult', $actual);
        $this->assertAttributeSame('UTF-8', 'charset', $actual);
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithUnknownCharset()
    {
        $config = new ConfigurationTestFixture(['charset' => 'unknown']);
        $actual = $this->subject->setConfiguration($config)->setup();
        $this->assertAttributeSame('auto', 'charset', $actual);
    }

    /**
     * @covers ::setup
     */
    public function testSetupWithInvalidCharsetThrowsException()
    {
        $this->subject->setConfiguration(
            new ConfigurationTestFixture(['charset' => 'invalid'])
        );

        $this->expectException(ConfigurationFailure::class);
        $this->expectExceptionMessage('Invalid configuration value for key "charset": invalid');
        $this->expectExceptionCode(1521291497);

        $this->subject->setup();
    }

    /**
     * @covers ::evaluateWithDependency
     * @testWith [true, "27c3a4c3b6c3bc27"]
     *           [false, "27c3a4c3b6c3bc27", "ISO-8859-1"]
     *           [false, "27c3a4c3b6c3bc27", "UTF-8", "ISO-8859-1", "ISO-8859-1"]
     *           [false, "27c3a4c3b6c3bc27", "UTF-8", "UTF-8", "UTF-8", "Windows"]
     *           [true, "22c3a4c3b6c3bc22", "UTF-8", "UTF-8", "UTF-8", "Windows"]
     *           [true, "27c3a4c3b6c3bc27", "UTF-8", "ISO-8859-1", "ISO-8859-1", "Linux", 50599]
     *
     * @param boolean $expect
     * @param string $charset
     * @param string $escapeshellarg
     * @param array $dependencies
     */
    public function testEvaluateWithDependency(
        bool $expect,
        string $escapeshellarg,
        string $charset = 'UTF-8',
        string $localeCharset = 'UTF-8',
        string $defaultCharset = 'UTF-8',
        string $osFamily = 'Linux',
        int $verionId = 70000)
    {
        $this->subject->setConfiguration(new ConfigurationTestFixture(['charset' => $charset]))->setup();

        $namespace = 'Sjorek\\RuntimeCapability\\Detection';
        $GLOBALS[$namespace]['escapeshellarg'] = hex2bin($escapeshellarg);
        $this->subject->setDependencyResults(
            ["os-family" => $osFamily, "version-id" => $verionId],
            [LC_CTYPE => $localeCharset],
            $defaultCharset
        );

        $actual = $this->subject->detect();
        $this->assertInternalType('boolean', $actual);
        $this->assertSame($expect, $actual);

        unset($GLOBALS[$namespace]['escapeshellarg']);
    }
}
