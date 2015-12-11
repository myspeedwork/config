<?php

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

        if ($associate) {
            foreach ($env as $values) {
                foreach ($values as $name => $value) {
                    putenv("$name=$value");
                    $_ENV[$name]    = $value;
                    $_SERVER[$name] = $value;
                }
            }
        } else {
            foreach ($env as $name => $value) {
                putenv("$name=$value");
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }

        return $env;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'env' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
