<?php
namespace Sjorek\RuntimeCapability\Tests\Fixtures\Dependence;

use Sjorek\RuntimeCapability\Dependence\AbstractDepending;

class DependingTestFixture1 extends AbstractDepending
{
    /**
     * @var string
     */
    const IDENTIFIER = 'depending-fixture1';

    /**
     * @var string[]
     */
    const DEPENDENCIES = [DependableTestFixture::IDENTIFIER];
}

