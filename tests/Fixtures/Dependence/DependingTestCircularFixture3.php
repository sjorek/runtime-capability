<?php
namespace Sjorek\RuntimeCapability\Tests\Fixtures\Dependence;

use Sjorek\RuntimeCapability\Dependence\AbstractDepending;

class DependingTestCircularFixture3 extends AbstractDepending
{
    /**
     * @var string
     */
    const IDENTIFIER = 'circular-fixture3';

    /**
     * @var string[]
     */
    const DEPENDENCIES = [DependingTestCircularFixture1::IDENTIFIER];
}

