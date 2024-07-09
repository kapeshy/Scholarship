<?php
// connect to our database( our database credentials)
$host ="localhost";
$user ="root";
$password ="";
$dbname ="is";
$port ="3306";
$conn = new mysqli($host, $user,$password, $dbname, $port);

if($conn->connect_error){
    //stop execution
    die('Connection Failed : '.$conn->connect_error);
}
?>