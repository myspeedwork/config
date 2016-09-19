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

use Speedwork\Container\Container;

class ContainerNormalizer implements NormalizerInterface
{
    private $app;

    /**
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function normalize($value)
    {
        if (preg_match('{^%([a-z0-9_.]+)%$}', $value, $match)) {
            return isset($this->app[$match[1]]) ? $this->app[$match[1]] : $match[0];
        }

        $result = preg_replace_callback('{%%|%([a-z0-9_.]+)%}', [$this, 'callback'], $value, -1, $count);

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
            return '%%';
        }

        return isset($this->app[$matches[1]]) ? $this->app[$matches[1]] : $matches[0];
    }
}
