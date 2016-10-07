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

class EnvFileLoader extends AbstractLoader
{
    protected function read($resource)
    {
        return parse_ini_string(file_get_contents($resource), true, INI_SCANNER_RAW);
    }

    public function load($resource, $associate = false)
    {
        $env = parent::load($resource, $associate);
        $env = array_change_key_case($env, CASE_UPPER);

        $data = [];
        if ($associate) {
            foreach ($env as $values) {
                foreach ($values as $name => $value) {
                    $data[$name] = $this->convert($value);
                }
            }
        } else {
            foreach ($env as $name => $value) {
                $data[$name] = $this->convert($value);
            }
        }

        foreach ($data as $name => $value) {
            putenv("$name=$value");
            $_ENV[$name]    = $value;
            $_SERVER[$name] = $value;
        }

        return $env;
    }

    /**
     * Convert env variables to proper data types.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function convert($value)
    {
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
        $value = trim($value, '"');

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'env' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
