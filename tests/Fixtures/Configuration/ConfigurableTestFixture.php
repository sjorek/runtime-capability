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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Configuration;

use Sjorek\RuntimeCapability\Configuration\AbstractConfigurable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class ConfigurableTestFixture extends AbstractConfigurable
{
    /**
     * @var array
     */
    protected static $DEFAULT_CONFIGURATION = ['fixture' => 'fixture'];
}
