<?php
// db_connect.php

// SECURE FIX: Parse credentials from an external INI file
$config = parse_ini_file('config.ini');

$host = $config['db_host'];
$username = $config['db_user'];
$password = $config['db_pass'];
$database = $config['db_name'];

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>