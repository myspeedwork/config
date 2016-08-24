<?php

namespace Speedwork\Config\Normalizer;

use Speedwork\Container\Container;

class ContainerNormalizer implements NormalizerInterface
{
    private $di;

    /**
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function normalize($value)
    {
        if (preg_match('{^%([a-z0-9_.]+)%$}', $value, $match)) {
            return isset($this->di[$match[1]]) ? $this->di[$match[1]] : $match[0];
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

        return isset($this->di[$matches[1]]) ? $this->di[$matches[1]] : $matches[0];
    }
}
