<?php
$server = "db.szngjfzvznhrrsmwvbhw.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$pass = "";
$conn = new mysqli($server,$user,$pass,$dbname);
if(!$conn){
echo "error!: {$conn->connect_error}";
}
?>

