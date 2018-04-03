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

namespace Sjorek\RuntimeCapability\Utility;

/**
 * @param string $filename
 * @return boolean
 */
function file_exists($filename)
{
//     var_dump($filename);

    return 'dangling-symlink' !== basename($filename) ? \file_exists($filename) : false;
}

/**
 * @param string $filename
 * @return boolean
 */
function is_dir($filename)
{
//     var_dump($filename);

    return false === strpos(basename($filename), 'symlink') ? \is_dir($filename) : false;
}

/**
 * @param string $filename
 * @return boolean
 */
function is_file($filename)
{
//     var_dump($filename);

    return false === strpos(basename($filename), 'symlink') ? \is_file($filename) : false;
}

/**
 * @param string $filename
 * @return boolean
 */
function is_link($filename)
{
//     var_dump($filename);

    return false === strpos(basename($filename), 'symlink') ? \is_link($filename) : true;
}

/**
 * @return string|boolean
 */
function getcwd()
{
    return $GLOBALS[__NAMESPACE__]['getcwd'] ?? \getcwd();
}

/**
 * @param string $filename
 * @return string|boolean
 */
function realpath($filename)
{
    return $GLOBALS[__NAMESPACE__]['realpath'][$filename] ?? \realpath($filename);
}

/**
 * @param string $name
 * @return mixed
 */
function constant($name)
{
    return $GLOBALS[__NAMESPACE__]['constant'][$name] ?? \constant($name);
}