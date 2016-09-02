<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Speedwork\Config\Normalizer;

class ChainNormalizer implements NormalizerInterface
{
    private $normalizers = [];

    /**
     * @param array $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        array_map([$this, 'add'], $normalizers);
    }

    /**
     * @param Normalizer $normalizer
     */
    public function add(NormalizerInterface $normalizer)
    {
        $this->normalizers[] = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($value)
    {
        if (!is_scalar($value)) {
            return $value;
        }

        foreach ($this->normalizers as $normalizer) {
            $value = $normalizer->normalize($value);
        }

        return $value;
    }
}
