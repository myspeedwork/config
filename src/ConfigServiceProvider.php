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
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class ConfigServiceProvider extends ServiceProvider
{
    protected $repository = null;
    /**
     * Bootstrap the given application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function register(Container $di)
    {
        $items = [];

        $cache = $di->get('config.cache');
        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if ($cache && file_exists($cached = $cache.'/config.php')) {
            $items = require $cached;

            $loadedFromCache = true;
        }
        $this->repository = new Config($items);

        $di->set('config', $this->repository);
        $di->set('config.loader.file', $this);

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        if (!isset($loadedFromCache)) {
            $locations = $di->get('config.paths');
            if (is_array($locations)) {
                foreach ($locations as $location) {
                    $this->load($location);
                }
            }
        }
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param $location
     */
    public function load($file = null)
    {
        $file = new SplFileInfo($file);
        if (!$file->isFile()) {
            return $this->loadConfigurationFiles($file);
        }

        $files                                         = [];
        $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();

        foreach ($files as $key => $path) {
            $this->repository->set($key, require $path);
        }
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param $location
     */
    public function loadConfigurationFiles($location)
    {
        foreach ($this->getConfigurationFiles($location) as $key => $path) {
            $this->repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param $path
     *
     * @return array
     */
    protected function getConfigurationFiles($path)
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }
}
