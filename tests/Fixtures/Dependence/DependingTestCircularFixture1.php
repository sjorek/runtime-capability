<?php
namespace Sjorek\RuntimeCapability\Tests\Fixtures\Dependence;

use Sjorek\RuntimeCapability\Dependence\AbstractDepending;

class DependingTestCircularFixture1 extends AbstractDepending
{
    /**
     * @var string
     */
    const IDENTIFIER = 'circular-fixture1';

    /**
     * @var string[]
     */
    const DEPENDENCIES = [DependingTestCircularFixture2::IDENTIFIER];
}

