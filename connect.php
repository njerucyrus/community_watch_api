<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/1/17
 * Time: 2:55 PM
 */

class Connection
{
    protected $databaseName = 'community_watch';
    protected $databaseUser = 'root';
    protected $password = '';
    protected $databaseHost = 'localhost';
    protected $conn;

    function getConnection()
    {
        $this->conn = new mysqli(
            $this->databaseHost,
            $this->databaseUser,
            $this->password,
            $this->databaseName
        );

        if($this->conn)
        {
            return $this->conn;
        }

        else
        {
            echo 'Error occurred while connecting to the database';
            return null;
        }

    }

}