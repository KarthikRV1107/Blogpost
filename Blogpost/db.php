<?php
$server = "";
$port = "";
$dbname = "";
$user = "";
$pass = "";
$conn = pg_connect(
    "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require"
);

if (!$conn) {
    die("Database connection failed.");
}

?>


