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

use Speedwork\Container\Container;
use Speedwork\Container\ServiceProvider;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    protected $paths    = [];
    protected $items    = [];
    protected $cacheDir = null;

    public function __construct($paths = [], $items = [], $cacheDir = null)
    {
        $this->paths    = $paths;
        $this->items    = $items;
        $this->cacheDir = $cacheDir;
    }

    public function register(Container $app)
    {
        $app['config'] = function ($app) {
            return new Config($this->items);
        };

        $app['config.loader'] = function ($app) {
            $builder = new LoaderBuilder($app['config.paths'], $this->cacheDir);
            $builder->setContainer($app);

            return $builder;
        };

        if (!empty($this->paths)) {
            $app['config.loader']->load($this->paths, true);
        }
    }
}
