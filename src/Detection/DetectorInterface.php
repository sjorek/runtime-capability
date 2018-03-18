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

namespace Sjorek\RuntimeCapability\Capability\Detection;

use Sjorek\RuntimeCapability\Capability\Configuration\ConfigurableInterface;
use Sjorek\RuntimeCapability\Management\ManageableInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 *
 * @todo Check if we need to implement chdir() to circumvent exceeding maximum path length
 */
interface DetectorInterface extends ManageableInterface, ConfigurableInterface
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

    /**
     * @param DetectorManagerInterface $manager
     *
     * @return DetectorInterface
     */
    public function setManager(DetectorManagerInterface $manager): self;
}
