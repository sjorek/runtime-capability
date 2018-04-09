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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Dependence;

use Sjorek\RuntimeCapability\Dependence\AbstractDepending;

class DependingTestCircularFixture2 extends AbstractDepending
{
    /**
     * @var string
     */
    const IDENTIFIER = 'circular-fixture2';

    /**
     * @var string[]
     */
    const DEPENDENCIES = [DependingTestCircularFixture3::IDENTIFIER];
}
