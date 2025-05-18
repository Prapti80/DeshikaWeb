<?php
$servername = "localhost";
$username = "root";
$password = "";
$databasename = "deshika_db";

$conn = mysqli_connect($servername, $username, $password, $databasename);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");
?>