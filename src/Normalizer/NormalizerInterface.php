<?php

namespace Speedwork\Config\Normalizer;

interface NormalizerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value);
}
