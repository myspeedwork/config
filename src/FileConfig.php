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

use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class FileConfig
{
    protected $di;

    public function __construct(Container $di)
    {
        $this->di = $di;
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

        $files = [];

        $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();

        foreach ($files as $key => $path) {
            $this->repository->set($key, require $path);
        }

        return $this;
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

        return $this;
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
