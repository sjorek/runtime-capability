<?php
namespace Sjorek\RuntimeCapability\Utility;

/**
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 */
final class ConfigurationUtility
{
    /**
     * Known php types:
     * <pre>
     * "boolean"
     * "integer"
     * "double" ......................... also to be used in case of a float
     * "string"
     * "array"
     * "object"
     * "resource"
     * "resource (closed)" .............. as of PHP 7.2.0
     * "NULL"
     * "unknown type"
     * </pre>
     *
     * Additional types:
     * <pre>
     * "match:PATTERN"                    Match scalar or an object's classname with pattern.
     *                                    The pattern must not be enclosed with delimiters!
     * "object:Namespace\Class"           The value object must be an instance of given class.
     *
     * "instance:Namespace\Class"         Same as above.
     *
     * "inherit:Namespace\Class"          The value object must be an instance of a subclass of given class.
     *
     * "implement:Namespace\Interface"    The value object must implement given interface.
     *
     * "class:Namespace\Class"            The string-value must represent given classname or a subclass thereof.
     *
     * "subclass:Namespace\Class"         The string-value must represent a subclass of given classname.
     *
     * "interface:Namespace\Interface"    The string-value must represent given interface or a subclass thereof.
     * </pre>
     *
     * @param string $type
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return string
     * @see http://php.net/manual/en/function.gettype.php
     */
    public static function getTypeForValue(string $type, $value)
    {
        $payload = null;
        if (false !== strpos($type, ':')) {
            list($key, $payload) = explode(':', $type, 2);
        }
        $actual = gettype($value);
        switch($key) {
            case 'match':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is: "match:PATTERN".',
                            $key
                        ),
                        1521388632
                    );
                }
                if (in_array($actual, 'string', 'integer', 'double', 'object')) {
                    if ('object' === $actual) {
                        $value = get_class($value);
                    }
                    $result = preg_match(sprintf('/%s/u', $payload), (string) $value);
                    if (false === $result) {
                        throw new \InvalidArgumentException(
                            sprintf(
                                'Invalid payload for given type: %s. The format is: "match:PATTERN". Hint: The pattern must not be enclosed in delimiters.',
                                $key
                            ),
                            1521388642
                        );
                    }
                    if (0 < $result) {
                        return $type;
                    }
                    return sprintf('%s:%s', $actual, $value);
                }
                break;
            case 'object':
                if (null === $payload) {
                    break;
                }
            case 'instance':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is: "instance:Namespace\\Class".',
                            $key
                        ),
                        1521388634
                    );
                }
                if (!class_exists($payload, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid payload "%s" for given type: %s. The class does not exist.',
                            $payload,
                            $key
                        ),
                        1521388644
                    );
                }
                if ('object' === $actual) {
                    if (is_a($value, $payload, false)) {
                        return $type;
                    }
                    return 'object:' . get_class($value);
                }
                break;
            case 'inherit':
            case 'subclass':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is "%s:Namespace\\Class".',
                            $key,
                            $key
                        ),
                        1521388634
                    );
                }
                if (!class_exists($payload, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid payload "%s" for given type: %s. The class does not exist.',
                            $payload,
                            $key
                        ),
                        1521388644
                    );
                }
                if ('inherit' === $key && 'object' === $actual) {
                    if (is_subclass_of($value, $payload, false)) {
                        return $type;
                    }
                    return 'object:' . get_class($value);
                }
                if ('subclass' === $key && 'string' === $actual && class_exists($value, true)) {
                    if (is_subclass_of($value, $payload, true)) {
                        return $type;
                    }
                    return 'class:' . $value;
                }
                break;
            case 'class':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is "class:Namespace\\Class".',
                            $key
                        ),
                        1521388635
                    );
                }
                if (!class_exists($payload, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid payload "%s" for given type: %s. The class does not exist.',
                            $payload,
                            $key
                        ),
                        1521388644
                    );
                }
                if ('string' === $actual && class_exists($value, true)) {
                    if (is_a($value, $payload, true)) {
                        return $type;
                    }
                    return 'class:' . $value;
                }
                break;
            case 'implement':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is "implement:Namespace\\Interface".',
                            $key
                        ),
                        1521388636
                    );
                }
                if (!interface_exists($payload, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid payload "%s" for given type: %s. The interface does not exist.',
                            $payload,
                            $key
                        ),
                        1521388646
                    );
                }
                if ('object' === $actual) {
                    if (is_subclass_of($value, $payload, false)) {
                        return $type;
                    }
                    return 'object:' . get_class($value);
                }
                break;
            case 'interface':
                if (null === $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Missing payload for given type: %s. The format is "interface:Namespace\\Interface".',
                            $key
                        ),
                        1521388637
                    );
                }
                if (!interface_exists($payload, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid payload "%s" for given type: %s. The interface does not exist.',
                            $payload,
                            $key
                        ),
                        1521388646
                    );
                }
                if ('string' === $actual && interface_exists($value, true)) {
                    if (is_a($value, $payload, true)) {
                        return $type;
                    }
                    return 'interface:' . $value;
                }
                break;
            default:
                if (null !== $payload) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Got unexpected payload for given type: %s.',
                            $key
                        ),
                        1521388639
                    );
                }
        }

        return $actual;
    }
}

