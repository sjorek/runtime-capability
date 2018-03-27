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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Identification;

use Sjorek\RuntimeCapability\Identification\AbstractIdentifiable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class IdentifiableTestFixture2 extends AbstractIdentifiable
{
    /**
     * @var string
     */
    const IDENTIFIER = 'custom-identifier';
}
