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
