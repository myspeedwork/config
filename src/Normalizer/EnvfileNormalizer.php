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

use Symfony\Component\Config\FileLocatorInterface;

class EnvfileNormalizer extends EnvironmentNormalizer
{
    private $env;
    private $locator;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    protected function callback($matches)
    {
        if (!isset($matches[1])) {
            return $matches[0];
        }

        $this->load();

        if (!isset($this->env[$matches[1]])) {
            return $matches[0];
        }

        return $this->env[$matches[1]];
    }

    protected function load()
    {
        if (!is_null($this->env)) {
            return;
        }

        if (!$file = $this->locate()) {
            return;
        }

        $this->env = parse_ini_string(file_get_contents($file), false, INI_SCANNER_RAW) ?: [];
        $this->env = array_change_key_case($this->env, CASE_UPPER);
    }

    protected function locate()
    {
        foreach (['.env', 'Envfile', 'Envfile.dist'] as $file) {
            try {
                return $this->locator->locate($file);
            } catch (\InvalidArgumentException $e) {
                // there is a possibility there is no file to load
            }
        }
    }
}
