<?php
$dbhost     = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname     = "visa_db";
 
$connection = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname);
 
if (!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
 