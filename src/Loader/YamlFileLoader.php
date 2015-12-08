<?php

namespace Speedwork\Config\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends AbstractLoader
{
    /**
     * @param  $resource
     *
     * @return array
     */
    protected function read($resource)
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
