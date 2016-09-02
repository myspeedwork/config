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

interface NormalizerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value);
}
