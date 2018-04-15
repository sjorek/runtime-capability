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

namespace Sjorek\RuntimeCapability\Detection;

use Sjorek\RuntimeCapability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Utility\CharsetUtility;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class DefaultCharsetDetector extends AbstractDetector
{
    /**
     * {@inheritdoc}
     *
     * @see ConfigurableInterface::setup()
     */
    public function setup(): ConfigurableInterface
    {
        // the evaluation result must never be reduced, as it does not contain any booleans
        $this->configuration['compact-result'] = false;

        return parent::setup();
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return ($charset = ini_get('default_charset')) ? CharsetUtility::normalizeEncodingName($charset) : false;
    }
}
