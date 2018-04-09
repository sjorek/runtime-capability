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

namespace Sjorek\RuntimeCapability\Tests\Fixtures\Detection;

use Sjorek\RuntimeCapability\Detection\AbstractDependingDetector;

class DependingDetectorTestFixture1 extends AbstractDependingDetector
{
    /**
     * @var array
     */
    const DEPENDENCIES = ['detector-test'];

    /**
     * {@inheritdoc}
     *
     * @see AbstractDependingDetector::evaluateWithDependency()
     */
    protected function evaluateWithDependency(bool $detectorTest)
    {
        return $detectorTest;
    }
}
