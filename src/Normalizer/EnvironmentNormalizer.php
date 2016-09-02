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

class EnvironmentNormalizer implements NormalizerInterface
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function normalize($value)
    {
        $result = preg_replace_callback('{##|#([A-Z0-9_:]+)#}', [$this, 'callback'], $value, -1, $count);

        return $count ? $result : $value;
    }

    /**
     * @param array $matches
     *
     * @return mixed
     */
    protected function callback($matches)
    {
        if (!isset($matches[1])) {
            return $matches[0];
        }

        list($match, $default) = explode(':', $matches[1]);

        if (false !== $env = getenv($match)) {
            return $env;
        }

        return $default;
    }
}
