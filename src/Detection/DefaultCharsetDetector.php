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

namespace Sjorek\RuntimeCapability\Capability\Detection;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class DefaultCharsetDetector extends AbstractDetector
{
    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return ($charset = ini_get('default_charset')) ? strtoupper(strtr($charset, '-', '')) : null;
    }
}
