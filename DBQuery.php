<?php
namespace pyatakss\db;

use \PDO as PDO,
    \DBQueryInterface as DBQueryInterface,
    \DBConnectionInterface as DBConnectionInterface;

require_once "DBQueryInterface.php";

class DBQuery implements DBQueryInterface
{
    /* Instance of DBConnect */
    private $dbConnection = null;

    /* PDO statement */
    private $stmt = null;

    /* Result of query execution */
    private $result = null;

    /* Last query execution time */
    private $executionTime = 0;

    /**
     * Create new instance DBQuery.
     *
     * @param DBConnectionInterface $DBConnection
     */
    public function __construct(DBConnectionInterface $DBConnection)
    {
        $this->setDBConnection($DBConnection);
    }

    public function __destruct()
    {
        $this->stmt = null;
        $this->result = null;
        $this->dbConnection = null;
    }

    /**
     * Returns the DBConnection instance.
     *
     * @return DBConnectionInterface
     */
    public function getDBConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Change DBConnection.
     *
     * @param DBConnectionInterface $DBConnection
     *
     * @return void
     */
    public function setDBConnection(DBConnectionInterface $DBConnection)
    {
        $this->dbConnection = $DBConnection;
    }

    /**
     * Executes the SQL statement and returns query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed if successful, returns a PDOStatement on error false
     */
    public function query($query, $params = null)
    {
        if (null !== $params) {
            $this->stmt = $this->dbConnection->getPdoInstance()->prepare($query);

            foreach ($params as $k => $v) {
                $k = ":" . $k;
                $this->stmt->bindParam($k, $v);
            }

        } else {
            $start = microtime(true);

            $this->stmt = $this->dbConnection->getPdoInstance()->query($query);

            $this->executionTime = round(((microtime(true) - $start)/1000), 8);
        }

        if ($this->stmt) {
            $this->result = null;

            return $this->stmt;
        } else {

            return false;
        }
    }

    /**
     * Executes the SQL statement and returns all rows of a result set as an associative array
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryAll($query, array $params = null)
    {
        $this->stmt = $this->query($query, $params);

        $this->result = $this->stmt->fetchAll();

        return $this->result;
    }

    /**
     * Executes the SQL statement returns the first row of the query result
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryRow($query, array $params = null)
    {
        $this->stmt = $this->query($query, $params);

        $this->result = $this->stmt->fetch();

        return $this->result;
    }

    /**
     * Executes the SQL statement and returns the first column of the query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryColumn($query, array $params = null)
    {
        $this->stmt = $this->query($query, $params);

        $this->result = $this->stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        return $this->result;
    }


    /**
     * Executes the SQL statement and returns the first field of the first row of the result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed  column value
     */
    public function queryScalar($query, array $params = null)
    {
        $this->stmt = $this->query($query, $params);

        $this->result = $this->stmt->fetchColumn();

        return $this->result;
    }

    /**
     * Executes the SQL statement.
     * This method is meant only for executing non-query SQL statement.
     * No result set will be returned.
     *
     * @param string $query   sql query
     * @param array  $params  input parameters (name=>value) for the SQL execution
     *
     * @return integer number of rows affected by the execution.
     */
    public function execute($query, array $params = null)
    {
        $start = microtime(true);

        $this->stmt = $this->dbConnection->getPdoInstance()->prepare($query);

        if ($this->stmt->execute($params)) {
            $this->executionTime = round(((microtime(true) - $start)/1000), 8);

            return $this->stmt->rowCount();
        }

        return 0;
    }


    /**
     * Returns the last query execution time in seconds
     *
     * @return float query time in seconds
     */
    public function getLastQueryTime()
    {
        return $this->executionTime;
    }

    /**
     * Set fetch mode
     *
     * @param integer $pdoConst   One of the constants PDO::FETCH_*
     * @param string  $className  Name of returned class
     *
     * @return bool
     */
    public function setFetchMode($pdoConst, $className = null)
    {
        return $this->stmt->setFetchMode($pdoConst, $className);
    }
}