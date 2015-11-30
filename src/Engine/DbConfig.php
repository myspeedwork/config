<?php

/**
 * This file is part of the Speedwork package.
 *
 * @link http://github.com/speedwork
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Speedwork\Config\Engine;

use Cake\Core\Configure\ConfigEngineInterface;
use Speedwork\Core\Registry;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class DbConfig implements ConfigEngineInterface
{
    /**
     * The path this reader finds files on.
     *
     * @var string
     */
    protected $database = null;

    /**
     * site id to get the settings.
     *
     * @var int
     */
    protected static $_getsite = 1;

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
    public function __construct($database = null)
    {
        $this->database = $database;

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
        if ($this->database === null) {
            return [];
        }

        $_config = [];

        $res = $this->database->find('#__core_options', 'all', [
            'conditions' => ['fksiteid' => self::$_getsite],
            'cache'      => 'daily',
        ]);

        foreach ($res as $data) {
            $value = $data['option_value'];
            $check = json_decode($value, true);

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
    public function dump($key, array $data)
    {
        $contents = '<?php'."\n".'return '.var_export($data, true).';';

        $filename = $this->_getFilePath($key);

        return file_put_contents($filename, $contents);
    }
}
