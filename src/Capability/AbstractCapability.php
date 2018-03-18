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

namespace Sjorek\RuntimeCapability\Capability;

use Sjorek\RuntimeCapability\Capability\Detection\DetectorInterface;
use Sjorek\RuntimeCapability\Exception\CapabilityDetectionFailure;
use Sjorek\RuntimeCapability\Management\AbstractManageable;

/**
 * @author Stephan Jorek <stephan.jorek@gmail.com>
 */
abstract class AbstractCapability extends AbstractManageable implements CapabilityInterface
{
    /**
     * @var CapabilityManagerInterface
     */
    protected $manager = null;

    /**
     * @param CapabilityManagerInterface $manager
     *
     * @return CapabilityInterface
     */
    public function setManager(CapabilityManagerInterface $manager): CapabilityInterface
    {
        return parent::setManager($manager);
    }

    /**
     * @param array               $configuration
     * @param DetectorInterface[] ...$detectors
     *
     * @throws CapabilityDetectionFailure
     *
     * @return array|bool
     */
    protected function evaluate(...$detectors)
    {
        $identifiers = [];
        $instances = [];
        $results = [];
        foreach ($detectors as $detector) {
            foreach ($this->resolve($detector) as $id => $detector) {
                if (in_array($id, $identifiers, true)) {
                    throw new CapabilityDetectionFailure(
                        sprintf('Circular detector dependency for id: %s', $id),
                        1521250751
                    );
                }
                if (in_array($detector, $instances, true)) {
                    throw new CapabilityDetectionFailure(
                        sprintf('Circular detector dependency for instance: %s', get_class($detector)),
                        1521250755
                    );
                }
                $identifiers[] = $id;
                $instances[] = $detector;
                if (empty($detector->depends())) {
                    $results[$id] = $detector->detect();
                }
            }
        }
        $limit = count($instances) * static::MAXIMIMUM_EVALUATION_RETRIES + 1;
        while (0 < $limit && !empty($instances)) {
            $limit -= 1;
            $detector = array_shift($instances);
            if (isset($results[$detector->identify()])) {
                continue;
            }
            $dependencies = [];
            foreach ($detector->depends() as $id) {
                $id = $this->detectorManager->get($id)->identify();
                if (isset($results[$id])) {
                    $dependencies[] = $results[$id];
                    continue;
                }
                array_push($instances, $detector);
                continue 2;
            }
            $dependencies = array_map(
                function ($id) use ($results) {
                    return $results[$this->detectorManager->get($id)->identify()];
                },
                $detector->depends()
            );
            $results[$detector->identify()] = $detector->detect(...$dependencies);
        }
        if (0 === $limit) {
            throw new CapabilityDetectionFailure(
                'Detection evaluation retry limit reached.',
                1521250759
            );
        }

        return $results ?: false;
    }

    /**
     * @param DetectorInterface $detector
     */
    protected function resolve($detector)
    {
        foreach ($detector->depends() as $id) {
            yield from $this->resolve($this->detectorManager->get($id));
        }
        yield $detector->identify() => $detector;
    }
}