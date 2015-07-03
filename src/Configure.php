<?php

/**
 * This file is part of the Speedwork package.
 *
 * (c) 2s Technologies <info@2stechno.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Speedwork\Config;

use Cake\Core\Configure as BaseConfigure;
use Speedwork\Core\Registry;
use Speedwork\Database\Database;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Configure extends BaseConfigure
{
    /**
     * Initializes configure and runs the bootstrap process.
     * Bootstrapping includes the following steps:.
     *
     * - Setup App array in Configure.
     * - Include app/Config/core.php.
     * - Configure core cache configurations.
     * - Load App cache files.
     * - Include app/Config/bootstrap.php.
     * - Setup error/exception handlers.
     *
     * @param bool $boot
     */
    public static function bootstrap($boot = true)
    {
        if ($boot) {
            //connect to database if set
            $datasource = self::read('database.config');
            $datasource = ($datasource) ? $datasource : 'default';
            $config     = self::read('database.'.$datasource);
            $database   = false;

            if (is_array($config)) {
                $database = new Database();
                $db       = $database->connect($config);
                if (!$db) {
                    if (php_sapi_name() == 'cli') {
                        echo json_encode([
                            'status'  => 'ERROR',
                            'message' => 'database was gone away',
                            'error'   => $database->lastError(),
                        ]);
                    } else {
                        $path = SYS.'public'.DS.'templates'.DS.'system'.DS.'databasegone.tpl';
                        echo @file_get_contents($path);
                        die('<!-- Database was gone away... -->');
                    }
                }
            }
            Registry::set('database', $database);

            register_shutdown_function(function () use ($database) {
                $database->disConnect();
            });
        }
    }
}
