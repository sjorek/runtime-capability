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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

/**
 * @param string $filename
 */
function file_exists($filename)
{
//     var_dump($filename);

    return 'dangling-symlink' !== basename($filename) ? \file_exists($filename) : false;
}

/**
 * @param string $filename
 */
function is_file($filename)
{
//     var_dump($filename);

    return false === strpos(basename($filename), 'symlink') ? \is_file($filename) : false;
}

/**
 * @param string $filename
 */
function is_link($filename)
{
//     var_dump($filename);

    return false === strpos(basename($filename), 'symlink') ? \is_link($filename) : true;
}
