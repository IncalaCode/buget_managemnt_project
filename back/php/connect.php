<?php
function connect()
{
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'buget_managment';

    $con = new mysqli($servername, $username, $password, $dbname);

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    return $con;
}