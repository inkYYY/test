<?php
namespace pyatakss\db;

use \PDO as PDO,
    \DBConnectionInterface as DBConnectionInterface;

require_once 'DBConnectionInterface.php';

class DBConnect implements DBConnectionInterface
{
    /**
     *  DBs connections
     *
     * [ '$dsn' => [
     *              '$username' =>  [
     *                               'dbh' => '$dbh',
     *                               'config' => [
     *                                             'username' => '',
     *                                             'password' => ''
     *                                            ]
     *                               ]
     *             ]
     *
     * ]
     */
    private static $connections = [];

    /* DB handler of current connection */
    private $currentDBh = null;

    /* Configuration of current connection */
    private $currentConfig = [
        'dsn' => '',
        'username' => '',
        'password' => ''
    ];

    /* ID of the row that last inserted */
    private $lastInsertID = null;

    /* Connect options */
    private static $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    private function __construct($dbh, $dsn, $username, $password)
    {
        $this->currentConfig = [
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password
        ];
        $this->currentDBh = $dbh;
    }

    public function __destruct()
    {
        $this->close();
    }

    private function __clone()
    {
    }

    /**
     * Creates new instance representing a connection to a database
     *
     * @param string $dsn       The Data Source Name, or DSN, contains the information required to connect to the database.
     * @param string $username  The user name for the DSN string.
     * @param string $password  The password for the DSN string.
     *
     * @see http://www.php.net/manual/en/function.PDO-construct.php
     * @throws  PDOException if the attempt to connect to the requested database fails.
     *
     * @return $this DB
     */
    public static function connect($dsn, $username = '', $password = '')
    {
        /* If connection NOT exists */
        if (!array_key_exists($dsn, self::$connections) ||
            (array_key_exists($dsn, self::$connections) && !array_key_exists($username, self::$connections[$dsn]))) {
                self::$connections[$dsn][$username]['config'] = [
                    'username' => $username,
                    'password' => $password
                ];

            try {
                self::$connections[$dsn][$username]['dbh'] = new PDO($dsn, $username, $password, self::$options);
            } catch (\PDOException $e) {
                self::$connections[$dsn][$username]['dbh'] = false;
                file_put_contents('logs.log', $e->getTraceAsString() . "\n", FILE_APPEND);
            }
        }

        return new self(self::$connections[$dsn][$username]['dbh'], $dsn, $username, $password);
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->close();

        extract($this->currentConfig);

        try {
            self::$connections[$dsn][$username]['dbh'] = new PDO($dsn, $username, $password, self::$options);
        } catch (\PDOException $e) {
            self::$connections[$dsn][$username]['dbh'] = [];
            file_put_contents('logs.log', $e->getTraceAsString() . "\n", FILE_APPEND);
        }

        $this->currentDBh = self::$connections[$dsn][$username]['dbh'];
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        return $this->currentDBh;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sequenceName name of the sequence object (required by some DBMS)
     *
     * @return string the row ID of the last row inserted, or the last value retrieved from the sequence object
     * @see http://www.php.net/manual/en/function.PDO-lastInsertId.php
     */
    public function getLastInsertID($sequenceName = '')
    {
        $this->lastInsertID = ($this->currentDBh->lastInsertId($sequenceName) == 0) ? $this->lastInsertID : $this->currentDBh->lastInsertId($sequenceName) ;
        return $this->lastInsertID;
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        $this->currentDBh = null;
        self::$connections[$this->currentConfig['dsn']][$this->currentConfig['username']]['dbh'] = null;
    }

    /**
     * Sets an attribute on the database handle.
     * Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * @param int $attribute
     * @param mixed $value
     *
     * @return bool
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function setAttribute($attribute, $value)
    {
        $this->currentDBh->setAttribute($attribute, $value);
    }

    /**
     * Returns the value of a database connection attribute.
     *
     * @param int $attribute
     *
     * @return mixed
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function getAttribute($attribute)
    {
        return $this->currentDBh->getAttribute($attribute);
    }

    /**
     * Get connections configuration
     *
     * @return  array   All configurations if null or name not founded
     */
    public function getConnectionConfig()
    {
        return self::$connections;
    }

    /**
     * Get current conf
     *
     * @return array
     */
    public function getCurrentConfiguration()
    {
        return $this->currentConfig;
    }

    /**
     * Get current DSN
     *
     * @return string
     */
    public function getCurrentDsn()
    {
        return $this->currentConfig['dsn'];
    }
}