<?php
$server = "db.szngjfzvznhrrsmwvbhw.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$pass = " fDhiuTMv1iV2sLlm";
$conn = pg_connect(
    "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require"
);

if (!$conn) {
    die("Database connection failed.");
}

?>


