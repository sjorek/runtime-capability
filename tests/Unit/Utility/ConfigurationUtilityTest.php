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

namespace Sjorek\RuntimeCapability\Tests\Unit\Utility;

use Sjorek\RuntimeCapability\Tests\Fixtures\Utility\ConfigurationUtilityTestFixtureClass1 as FixtureClass1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Utility\ConfigurationUtilityTestFixtureClass2 as FixtureClass2;
use Sjorek\RuntimeCapability\Tests\Fixtures\Utility\ConfigurationUtilityTestFixtureInterface1 as FixtureInterface1;
use Sjorek\RuntimeCapability\Tests\Fixtures\Utility\ConfigurationUtilityTestFixtureInterface2 as FixtureInterface2;
use Sjorek\RuntimeCapability\Tests\Unit\AbstractTestCase;
use Sjorek\RuntimeCapability\Utility\ConfigurationUtility;

/**
 * ConfigurationUtility test case.
 *
 * @coversDefaultClass \Sjorek\RuntimeCapability\Utility\ConfigurationUtility
 */
class ConfigurationUtilityTest extends AbstractTestCase
{
    /**
     * @covers       ::getTypeForValue
     * @dataProvider provideTestGetTypeForValueData
     *
     * @param mixed $value
     */
    public function testGetTypeForValue(string $expect, string $type, $value)
    {
        $this->assertSame($expect, ConfigurationUtility::getTypeForValue($type, $value));
    }

    public function provideTestGetTypeForValueData()
    {
        return [
            'boolean' => [
                'boolean', 'boolean', true,
            ],
            'not boolean' => [
                'null', 'boolean', null,
            ],
            'integer' => [
                'integer', 'integer', 0,
            ],
            'not integer' => [
                'null', 'integer', null,
            ],
            // "double" (for historical reasons "double" is returned in case of a float, and not simply "float")
            'double' => [
                'double', 'double', 1.0,
            ],
            'not double' => [
                'null', 'double', null,
            ],
            // 'float' => [
            //     'float', 'float', 1.0,
            // ],
            // 'not float' => [
            //     'null', 'float', null,
            // ],
            'string' => [
                'string', 'string', 'a string',
            ],
            'not string' => [
                'null', 'string', null,
            ],
            'array' => [
                'array', 'array', [],
            ],
            'not array' => [
                'null', 'array', null,
            ],
            'object' => [
                'object', 'object', new \stdClass(),
            ],
            'not object' => [
                'null', 'object', null,
            ],
            'resource' => [
                'resource', 'resource', fopen('php://memory', 'r+'),
            ],
            'not resource' => [
                'null', 'resource', null,
            ],
            '"resource (closed)" as of PHP 7.2.0' => [
                'resource', 'resource', (function ($r) {
                    fclose($r);
                    return $r;
                })(fopen('php://memory', 'r+')),
            ],
            'null' => [
                'null', 'null', null,
            ],
            'not null' => [
                'string', 'null', '',
            ],
            'match:integer' => [
                'match:^1$', 'match:^1$', 1,
            ],
            'not match:integer' => [
                'integer:2', 'match:^1$', 2,
            ],
            'match:double' => [
                'match:^1\\.2$', 'match:^1\\.2$', 1.2,
            ],
            'not match:double' => [
                'double:1.1', 'match:^1\\.2$', 1.1,
            ],
            'match:string' => [
                'match:^a string$', 'match:^a string$', 'a string',
            ],
            'not match:string' => [
                'string:another string', 'match:^a string$', 'another string',
            ],
            'match:object' => [
                'match:^stdClass$', 'match:^stdClass$', new \stdClass(),
            ],
            'not match:object' => [
                'object:' . FixtureClass1::class, 'match:^stdClass$', new FixtureClass1(),
            ],
            'integer:match' => [
                'integer:^1$', 'integer:^1$', 1,
            ],
            'not integer:match' => [
                'integer:2', 'integer:^1$', 2,
            ],
            'double:match' => [
                'double:^1\\.2$', 'double:^1\\.2$', 1.2,
            ],
            'not double:match' => [
                'double:1.1', 'double:^1\\.2$', 1.1,
            ],
            'string:match' => [
                'string:^a string$', 'string:^a string$', 'a string',
            ],
            'not string:match' => [
                'string:another string', 'string:^a string$', 'another string',
            ],
            'array:match' => [
                'array:^[0-9]$', 'array:^[0-9]$', range(0, 9),
            ],
            'not array:match' => [
                'array', 'array:^[0-9]$', range(1, 10),
            ],
            'object:ClassName' => [
                'object:' . FixtureClass1::class, 'object:' . FixtureClass1::class, new FixtureClass2(),
            ],
            'not object:ClassName' => [
                'object:' . \stdClass::class, 'object:' . FixtureClass1::class, new \stdClass(),
            ],
            'instance:ClassName' => [
                'instance:' . FixtureClass1::class, 'instance:' . FixtureClass1::class, new FixtureClass2(),
            ],
            'not instance:ClassName' => [
                'instance:' . \stdClass::class, 'instance:' . FixtureClass1::class, new \stdClass(),
            ],
            'inherit:ClassName' => [
                'inherit:' . FixtureClass1::class, 'inherit:' . FixtureClass1::class, new FixtureClass2(),
            ],
            'not inherit:ClassName' => [
                'object:' . FixtureClass1::class, 'inherit:' . FixtureClass2::class, new FixtureClass1(),
            ],
            'implement:InterfaceName' => [
                'implement:' . FixtureInterface1::class, 'implement:' . FixtureInterface1::class, new FixtureClass2(),
            ],
            'not implement:InterfaceName' => [
                'object:' . FixtureClass1::class, 'implement:' . FixtureInterface2::class, new FixtureClass1(),
            ],
            'class:ClassName' => [
                'class:' . FixtureClass1::class, 'class:' . FixtureClass1::class, FixtureClass1::class,
            ],
            'not class:ClassName' => [
                'class:' . \stdClass::class, 'class:' . FixtureClass1::class, \stdClass::class,
            ],
            'subclass:ClassName' => [
                'subclass:' . FixtureClass1::class, 'subclass:' . FixtureClass1::class, FixtureClass2::class,
            ],
            'not subclass:ClassName' => [
                'class:' . FixtureClass1::class, 'subclass:' . FixtureClass1::class, FixtureClass1::class,
            ],
            'interface:InterfaceName' => [
                'interface:' . FixtureInterface1::class, 'interface:' . FixtureInterface1::class, FixtureInterface2::class,
            ],
            'not interface:InterfaceName' => [
                'interface:' . FixtureInterface1::class, 'interface:' . FixtureInterface2::class, FixtureInterface1::class,
            ],
            // "unknown type" ?
        ];
    }

    /**
     * @covers ::getTypeForValue
     */
    public function testGetTypeForValueWithEmptyPayloadThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid payload given, it must not be empty.');
        $this->expectExceptionCode(1521388630);
        ConfigurationUtility::getTypeForValue('xxx:', null);
    }

    /**
     * @covers       ::getTypeForValue
     * @dataProvider provideTestGetTypeForValueWithUnexpectedPayloadThrowsInvalidArgumentExceptionData
     *
     * @param string $key
     * @param string $type
     */
    public function testGetTypeForValueWithUnexpectedPayloadThrowsInvalidArgumentException(string $key, string $type)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Unexpected payload for given type: %s. The format is: "%s".', $key, $key)
        );
        $this->expectExceptionCode(1521388631);
        ConfigurationUtility::getTypeForValue($type, null);
    }

    public function provideTestGetTypeForValueWithUnexpectedPayloadThrowsInvalidArgumentExceptionData()
    {
        return [
            'boolean' => [
                'boolean', 'boolean:xyz',
            ],
            // 'integer' => [
            //     'integer', 'integer:xyz',
            // ],
            // "double" (for historical reasons "double" is returned in case of a float, and not simply "float")
            // 'double' => [
            //     'double', 'double:xyz',
            // ],
            // 'float' => [
            //     'float', 'float:xyz',
            // ],
            // 'string' => [
            //     'string', 'string:xyz',
            // ],
            // 'array' => [
            //     'array', 'array:xyz',
            // ],
            'resource' => [
                'resource', 'resource:xyz',
            ],
            'NULL' => [
                'null', 'null:xyz',
            ],
            // "unknown type" ?
        ];
    }

    /**
     * @covers ::getTypeForValue
     */
    public function testGetTypeForValueWithMatchWithoutPatternThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing payload for given type: match. The format is: "match:PATTERN".');
        $this->expectExceptionCode(1521388632);
        ConfigurationUtility::getTypeForValue('match', null);
    }

    /**
     * @covers ::getTypeForValue
     */
    public function testGetTypeForValueWithMatchWithInvalidPatternThrowsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp(
            '/^Invalid payload for given type: match\\. The format is: "match:PATTERN"\\. Code: 1\\./'
        );
        $this->expectExceptionCode(1521388642);
        ConfigurationUtility::getTypeForValue('match:(', '');
    }

    /**
     * @covers       ::getTypeForValue
     * @dataProvider provideTestGetTypeForValueRequiredArgumentThrowsInvalidArgumentExceptionData
     *
     * @param string $key
     * @param string $type
     */
    public function testGetTypeForValueWithoutRequiredArgumentThrowsInvalidArgumentException(
        string $key, string $type
    ) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Missing payload for given type: %s. The format is: "%s:Namespace\\%s".',
                $key,
                $key,
                $type
            )
        );
        $this->expectExceptionCode(1521388634);
        ConfigurationUtility::getTypeForValue($key, null);
    }

    /**
     * @return string[][]
     */
    public function provideTestGetTypeForValueRequiredArgumentThrowsInvalidArgumentExceptionData()
    {
        return [
            'instance' => ['instance', 'Class'],
            'inherit' => ['inherit', 'Class'],
            'class' => ['class', 'Class'],
            'subclass' => ['subclass', 'Class'],

            'implement' => ['implement', 'Interface'],
            'interface' => ['interface', 'Interface'],
        ];
    }

    /**
     * @covers       ::getTypeForValue
     * @dataProvider provideTestGetTypeForValueWithNonExistingArgumentThrowsInvalidArgumentExceptionData
     *
     * @param string $key
     */
    public function testGetTypeForValueWithNonExistingArgumentThrowsInvalidArgumentException(
        string $key, string $type
    ) {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Invalid payload "Non\\Existent" for given type: %s. The %s does not exist.',
                $key,
                $type
            )
        );
        $this->expectExceptionCode(1521388644);
        ConfigurationUtility::getTypeForValue($key . ':Non\\Existent', null);
    }

    /**
     * @return string[][]
     */
    public function provideTestGetTypeForValueWithNonExistingArgumentThrowsInvalidArgumentExceptionData()
    {
        return array_map(
            function ($value) {
                $value[1] = strtolower($value[1]);

                return $value;
            },
            $this->provideTestGetTypeForValueRequiredArgumentThrowsInvalidArgumentExceptionData()
        );
    }
}
