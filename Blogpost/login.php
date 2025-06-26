<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
</head>
<body>
<div class="login-container">
  <h2>Login</h2>
  <form action="login.php" method="POST">
    <input type="text" name="name" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="submit" value="Login">
  </form>
</div>
</body>
</html>

<?php
session_start();
include "db.php";
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM USERS WHERE name = '$name'";
    $result = mysqli_query($conn, $sql);
    if(!$result){
        echo "Error!: {$conn->error}";
    }
    else{
        if($result->num_rows > 0){
            $row=mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            echo "<br> Loged in Successfully ! <br> <a href= 'dashboard.php'>Dashboard</a>";
        }
    }
}
?>