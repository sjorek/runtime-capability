<?php
namespace Sjorek\RuntimeCapability\Dependence;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DependencyInterface extends DependableInterface
{
    /**
     * @return string[]|DependableInterface[]
     */
    public function depends();
}

