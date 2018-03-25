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

use Sjorek\RuntimeCapability\Dependence\DependencyInterface;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
interface DependingDetectorInterface extends DetectorInterface, DependencyInterface
{
    /**
     * @return string[]
     */
    public function depends();

    /**
     * @param array[bool[]]|bool[]|bool ...$dependencies
     *
     * @return array[bool[]]|bool[]|bool
     */
    public function detect(...$dependencies);
}
