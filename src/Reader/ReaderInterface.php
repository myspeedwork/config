<?php

/**
 * This file is part of the Speedwork package.
 *
 * (c) 2s Technologies <info@2stechno.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Speedwork\Config\Reader;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
interface ReaderInterface
{
    /**
     * Read method is used for reading configuration information from sources.
     * These sources can either be static resources like files, or dynamic ones like
     * a database, or other datasource.
     *
     * @param string $key
     *
     * @return array An array of data to merge into the runtime configuration
     */
    public function read($key);
}
