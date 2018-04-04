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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target;

/**
 * @param string $filename
 */
function symlink($target, $link)
{
    if (false === strpos($link, 'vfs://')) {
        return \symlink($target, $link);
    }
    $data = sprintf('symlink: %s', $target);

    return strlen($data) === file_put_contents($link, $data);
}
