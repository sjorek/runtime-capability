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

namespace Sjorek\RuntimeCapability\Management;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractManageable implements ManageableInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     *
     * @see ManageableInterface::id()
     */
    public function identify(): string
    {
        if (null !== $this->id) {
            return $this->id;
        }
        $className = static::class;
        if (defined($className . '::CAPABILITY_ID')) {
            return $this->id = (string) static::CAPABILITY_ID;
        }
        $id = $this->extractId($className);

        return $this->id = $id === $className ? $id : $this->normalizeId($id);
    }

    /**
     * {@inheritdoc}
     *
     * @see ManageableInterface::setManager()
     */
    public function setManager(ManagerInterface $manager): ManageableInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    protected function extractId($id)
    {
        return preg_replace(self::EXTRACT_ID_PATTERN, '$1', $id);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    protected function normalizeId($id)
    {
        return strtolower(preg_replace(self::NORMALIZE_ID_PATTERN, '-$0', $id));
    }
}
