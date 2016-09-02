<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Speedwork\Config\Loader;

use Speedwork\Config\Normalizer\NormalizerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;

class NormalizerLoader extends \Symfony\Component\Config\Loader\Loader
{
    private $normalizer;
    private $loader;

    public function __construct(LoaderInterface $loader, NormalizerInterface $normalizer)
    {
        $this->loader     = $loader;
        $this->normalizer = $normalizer;
    }

    public function load($resource, $type = null)
    {
        $parameters = $this->loader->load($resource, $type);

        return array_map([$this, 'normalize'], $parameters);
    }

    public function supports($resource, $type = null)
    {
        return $this->loader->supports($resource, $type);
    }

    private function normalize($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'normalize'], $value);
        }

        return $this->normalizer->normalize($value);
    }
}
