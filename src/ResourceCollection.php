<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Speedwork\Config;

use Symfony\Component\Config\Resource\ResourceInterface;

class ResourceCollection
{
    private $resources = [];

    public function add(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    public function all()
    {
        return $this->resources;
    }
}
