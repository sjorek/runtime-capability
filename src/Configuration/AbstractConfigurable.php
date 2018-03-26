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

namespace Sjorek\RuntimeCapability\Capability\Configuration;

use Sjorek\RuntimeCapability\Identification\IdentifiableInterface;
use Sjorek\RuntimeCapability\Identification\IdentifiableTrait;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractConfigurable implements ConfigurableInterface, IdentifiableInterface
{
    use IdentifiableTrait;
    use ConfigurableTrait;
}
