<?php

/**
 * This file is part of the Speedwork package.
 *
 * (c) 2s Technologies <info@2stech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Speedwork\Config\Reader;

use Exception;

/**
 * @author sankar <sankar.suda@gmail.com>
 */

/**
 * PHP Reader allows Configure to load configuration values from
 * files containing simple PHP arrays.
 *
 * Files compatible with PhpReader should define a `$config` variable, that
 * contains all of the configuration data contained in the file.
 */
class PhpReader implements ReaderInterface
{
    /**
     * The path this reader finds files on.
     *
     * @var string
     */
    protected $_path = null;

    /**
     * Constructor for PHP Config file reading.
     *
     * @param string $path The path to read config files from.  Defaults to APP . 'Config' . DS
     */
    public function __construct($path = null)
    {
        if (!$path) {
            $path = APP.'config'.DS;
        }
        $this->_path = $path;
    }

    /**
     * Read a config file and return its contents.
     *
     * Files with `.` in the name will be treated as values in plugins.  Instead of reading from
     * the initialized path, plugin keys will be located using App::pluginPath().
     *
     * @param string $key The identifier to read from.  If the key has a . it will be treated
     *                    as a plugin prefix.
     *
     * @return array Parsed configuration values.
     *
     * @throws ConfigureException when files don't exist or they don't contain `$config`.
     *                            Or when files contain '..' as this could lead to abusive reads.
     */
    public function read($key)
    {
        if (strpos($key, '..') !== false) {
            throw new Exception(_e('Cannot load configuration files with ../ in them.'));
        }
        if (substr($key, -4) === '.php') {
            $key = substr($key, 0, -4);
        }

        if (strpos($key, 'system.') !== false) {
            $key = str_replace('system.', '', $key);
            $key = SYS.$key;
        } else {
            $key = APP.$key;
        }

        $file = $key;
        $file .= '.php';

        if (!is_file($file)) {
            if (!is_file(substr($file, 0, -4))) {
                throw new ConfigureException(_e('Could not load configuration files: %s or %s', [$file, substr($file, 0, -4)]));
            }
        }

        $_config = [];

        include $file;

        return $_config;
    }

    /**
     * Converts the provided $data into a string of PHP code that can
     * be used saved into a file and loaded later.
     *
     * @param string $filename The filename to create on $this->_path.
     * @param array  $data     Data to dump.
     *
     * @return int Bytes saved.
     */
    public function dump($filename, $data)
    {
        $contents = '<?php'."\n".'$_config = '.var_export($data, true).';';

        return file_put_contents($this->_path.$filename, $contents);
    }
}
