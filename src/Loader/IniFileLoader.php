<?php

namespace Speedwork\Config\Loader;

class IniFileLoader extends AbstractLoader
{
    protected function read($resource)
    {
        return parse_ini_string(file_get_contents($resource), true, INI_SCANNER_RAW);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'ini' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
