<?php
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

