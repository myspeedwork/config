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

use Speedwork\Config\ResourceCollection;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;

class CacheLoader extends \Symfony\Component\Config\Loader\Loader
{
    private $loader;
    private $resources;
    private $debug = false;
    private $cacheDir;

    public function __construct(LoaderInterface $loader, ResourceCollection $resources)
    {
        $this->loader    = $loader;
        $this->resources = $resources;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function load($resource, $associate = false)
    {
        $cache = new ConfigCache(sprintf('%s/%s.php', $this->cacheDir, crc32($resource)), $this->debug);

        if (!$cache->isFresh()) {
            $parameters = $this->loader->load($resource, $associate);
        }

        if ($this->cacheDir && isset($parameters)) {
            $cache->write('<?php $parameters = '.var_export($parameters, true).';', $this->resources->all());
        }

        if (!isset($parameters)) {
            require method_exists($cache, 'getPath') ? $cache->getPath() : (string) $cache;
        }

        return $parameters;
    }

    public function supports($resource, $type = null)
    {
        return $this->loader->supports($resource, $type);
    }
}
