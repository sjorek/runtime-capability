<?php

declare(strict_types=1);

/*
 * This file is part of the Unicode Normalization project.
 *
 * (c) Stephan Jorek <stephan.jorek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sjorek\RuntimeCapability\Capability\Filesystem\Driver;

use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractFilesystemDriver extends AbstractManageable
{
    /**
     * @var FilesystemDriverManagerInterface
     */
    protected $manager = null;

    /**
     * {@inheritdoc}
     *
     * @param FilesystemDriverManagerInterface $manager
     *
     * @return FilesystemDriverInterface
     *
     * @see FilesystemDriverInterface::setManager()
     */
    public function setManager(FilesystemDriverManagerInterface $manager): FilesystemDriverInterface
    {
        return parent::setManager($manager);
    }
}
