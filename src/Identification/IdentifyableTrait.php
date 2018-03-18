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

namespace Sjorek\RuntimeCapability\Identification;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
trait IdentifyableTrait
{
    /**
     * @var string
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @see IdentifyableInterface::identify()
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
     * @param string $id
     *
     * @return string
     */
    protected function extractId($id)
    {
        return preg_replace(static::EXTRACT_ID_PATTERN, '$1', $id);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    protected function normalizeId($id)
    {
        return strtolower(preg_replace(static::NORMALIZE_ID_PATTERN, '-$0', $id));
    }
}
