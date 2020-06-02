<?php
/**
 * @author denis909
 * @license MIT
 */
namespace Denis909\Db;

class MySQLiAdapter implements AdapterInterface
{

    protected $_connection;

    public function __construct($config)
    {
        $this->_connection = @mysqli_connect($config->host, $config->user, $config->password, $config->db);

        if (mysqli_connect_errno()) 
        {
            $error = mysqli_connect_error();
       
            throw new DbException($error);
        }
        else
        {
            if ($config->charset)
            {
                mysqli_set_charset($this->_connection, $config->charset);
            }
        }
    }

    public function __destruct()
    {
        if ($this->_connection)
        {
            return mysqli_close($this->_connection);
        }        
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    public function query($sql)
    {
        $return = mysqli_query($this->_connection, $sql);

        if (!$return)
        {
            $error = mysqli_error($this->_connection);

            throw new DbException($error);
        }

        return $return;
    }

    public function queryOne($sql)
    {
        $result = $this->query($sql);

        $return = mysqli_fetch_array($result, MYSQLI_ASSOC);

        mysqli_free_result($result);

        return $return;
    }

    public function queryAll($sql)
    {
        $result = $this->query($sql);

        //return mysqli_fetch_all($result, MYSQLI_ASSOC);

        $return = [];

        while($row = $result->fetch_assoc())
        {
            $return[] = $row;
        }

        mysqli_free_result($result);

        return $return;
    }

    public function escape($sql)
    {
        return mysqli_real_escape_string($this->_connection, $sql);
    }

    public function insertId()
    {
        return mysqli_insert_id($this->_connection);
    }

    public function count($sql)
    {
        $result = $this->query($sql);

        $return = mysqli_num_rows($result);

        mysqli_free_result($result);

        return $return;
    }

}