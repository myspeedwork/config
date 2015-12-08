<?php

/**
 * This file is part of the Speedwork framework.
 *
 * @link http://github.com/speedwork
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Speedwork\Config;

use Speedwork\Container\Container;
use Speedwork\Container\ServiceProvider;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    public function register(Container $app)
    {
        $app['config'] = function ($app) {
            return new Config();
        };

        $app['config.loader'] = function ($app) {
            $builder = new LoaderBuilder($app['config.paths']);
            $builder->setContainer($app);

            return $builder;
        };
    }
}
