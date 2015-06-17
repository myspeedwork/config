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

use Speedwork\Core\Registry;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class DbReader implements ReaderInterface
{
    /**
     * The path this reader finds files on.
     *
     * @var string
     */
    protected $_database = null;

    /**
     * site id to get the settings.
     *
     * @var int
     */
    public static $_getsite = 1;
    /**
     * site id to update settings.
     *
     * @var int
     */
    public static $_upsite = 1;

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

        $this->_database = Registry::get('database');

        $siteid = Registry::get('configid');
        if ($siteid) {
            self::$_getsite = $siteid;
        }
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
    public function read($key = null)
    {
        $_config = [];

        if (!is_object($this->_database)) {
            return $_config;
        }

        $res = $this->_database->find('#__core_options', 'all', [
                'conditions' => ['fksiteid' => self::$_getsite],
                'cache'      => 'daily',
                ]
            );

        foreach ($res as $data) {
            $value = $data['option_value'];
            $check = @json_decode($value, true);

            $_config[$data['option_name']] = ($check == null || $check == false) ? $value : $check;
        }

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
