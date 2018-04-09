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

use Sjorek\RuntimeCapability\Dependence\DependingTrait;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractDependingDetector extends AbstractDetector implements DependingDetectorInterface
{
    use DependingTrait;

    /**
     * @var array[bool[]]|bool[]
     */
    protected $dependencyResults = [];

    /**
     * {@inheritdoc}
     *
     * @see DependingDetectorInterface::setDependencies()
     */
    public function setDependencyResults(...$results): DependingDetectorInterface
    {
        $this->dependencyResults = $results;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractDetector::evaluate()
     */
    protected function evaluate()
    {
        return $this->evaluateWithDependency(...$this->dependencyResults);
    }

    // /**
    //  * @throws \RuntimeException
    //  */
    // protected function evaluateWithDependency()
    // {
    //     throw new \Exception(
    //         'Missing implementation of method "evaluateWithDependency".',
    //         1523271351
    //     );
    // }
}
