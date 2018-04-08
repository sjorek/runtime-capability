<?php
namespace Sjorek\RuntimeCapability\Tests\Fixtures\Dependence;

use Sjorek\RuntimeCapability\Dependence\AbstractDepending;

class DependingTestFixture2 extends AbstractDepending
{
    /**
     * @var string
     */
    const IDENTIFIER = 'depending-fixture2';

    /**
     * @var string[]
     */
    const DEPENDENCIES = [DependableTestFixture::IDENTIFIER, DependingTestFixture1::IDENTIFIER];
}
