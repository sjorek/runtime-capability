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

namespace Sjorek\RuntimeCapability\Filesystem\Driver\PHP;

use Sjorek\RuntimeCapability\Filesystem\Driver\PHP\Target\FileTargetDriver;
use Sjorek\RuntimeCapability\Filesystem\Strategy\ExistingDirectoryStrategyInterface;

/**
 * Facade to filesystem specific functionality, providing a reduced interface to what is needed.
 *
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
class PHPExistingDirectoryDriver extends PHPCurrentDirectoryDriver implements ExistingDirectoryStrategyInterface
{
    /**
     * @param null|PHPFilesystemDriverInterface $targetDriver
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(PHPFilesystemDriverInterface $targetDriver = null)
    {
        if (null === $targetDriver) {
            $targetDriver = new FileTargetDriver();
        }
        if ($targetDriver instanceof ExistingDirectoryStrategyInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The driver must not implement the interface: %s',
                    ExistingDirectoryStrategyInterface::class
                ),
                1522331750
            );
        }
        parent::__construct($targetDriver);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Sjorek\RuntimeCapability\Filesystem\Strategy\ExistingDirectoryStrategyInterface::setDirectory()
     */
    public function setDirectory($path)
    {
        return $this->setWorkingDirectory($path);
    }
}
