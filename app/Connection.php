<?php

/**
 * PostgreSQL Connect
 *
 * PHP version 7.4
 *
 * @category Project
 * @package  Page_Analyzer
 * @author   toridnc <riadev@inbox.ru>
 * @license  MIT https://mit-license.org/
 * @link     https://github.com/toridnc/php-project-lvl3
 */

namespace PostgreSQLConnect;

/**
 * Connection — Singleton-class. Only one instance can be created for it.
 * If an instance already exists,
 * the class returns it when it attempts to create a new instance.
 *
 * @category Project
 * @package  Page_Analyzer
 * @author   toridnc <riadev@inbox.ru>
 * @license  MIT https://mit-license.org/
 * @link     https://github.com/toridnc/php-project-lvl3
 */
class Connection
{
    /**
     * Connection
     * Type @var
     */
    private static $conn;

    /**
     * Connect to the database and return an object instance \PDO
     *
     * @return \PDO
     * @throws \Exception
     */

    public function connect()
    {
        // Read settings in the ini configuration file
        $databaseUrl = parse_url($_ENV['DATABASE_URL']);
        if ($databaseUrl === false) {
            throw new \Exception("Error reading database configuration file");
        }
        // Connection to the PostgreSQL database
        $host = $databaseUrl['host'];
        $port = $databaseUrl['port'];
        $dbname = ltrim($databaseUrl['path'], '/');
        $username = $databaseUrl['user'];
        $password = $databaseUrl['pass'];

        $conStr = "pgsql:host=$host;port=$port;dbname=$dbname";
        $opt = array(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo = new \PDO($conStr, $username, $password, $opt);

        return $pdo;
    }
        /**
         * Return Instance of Connection Object
         *
         * Type @return
         *
         * @return Instance of Connection Object
         */
    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }
        /**
         * Protected function
         *
         * @return Protected function construct
         */
    protected function __construct()
    {
    }
}
