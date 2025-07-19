<?php
session_start();
include "db.php";

$error = "";

if (isset($_POST['name']) && isset($_POST['password'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM USERS WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $error = "Database error: {$conn->error}";
    } else {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $allowed_roles = ['author', 'admin'];
                if (in_array(strtolower($row['role']), $allowed_roles)) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['name'];
                    $_SESSION['user_role'] = $row['role'];

                    header("Location: blogpost.php");
                    exit();
                } else {
                    $error = "You do not have permission to access this area.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary-color: #6366f1;
      --primary-dark: #4f46e5;
      --secondary-color: #f43f5e;
      --light-color: #f8fafc;
      --dark-color: #1e293b;
      --gray-color: #64748b;
      --border-radius: 12px;
      --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f1f5f9;
      color: var(--dark-color);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      line-height: 1.5;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      margin: 2rem;
    }

    .login-header {
      position: relative;
      height: 180px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 2rem;
      color: white;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .login-header::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-image: url('https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/a2d993e9-f132-4d59-bf2d-6daf75d0f682.png');
      background-size: cover;
      background-position: center;
      opacity: 0.2;
      z-index: 0;
    }

    .login-header h2 {
      font-size: 1.75rem;
      font-weight: 700;
      position: relative; z-index: 1;
    }

    .login-header p {
      opacity: 0.9;
      position: relative; z-index: 1;
      font-size: 0.875rem;
    }

    .login-form {
      padding: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      color: var(--gray-color);
    }

    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      border: 1px solid #e2e8f0;
      border-radius: var(--border-radius);
      transition: var(--transition);
      background-color: #f8fafc;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .btn {
      display: inline-block;
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      font-weight: 500;
      color: white;
      background-color: var(--primary-color);
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
    }

    .btn:hover {
      background-color: var(--primary-dark);
    }

    .form-footer {
      margin-top: 1.5rem;
      text-align: center;
      font-size: 0.875rem;
      color: var(--gray-color);
    }

    .form-footer a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 70%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--gray-color);
      font-size: 0.875rem;
    }

    .error-message {
      margin-top: 1rem;
      color: var(--secondary-color);
      text-align: center;
      font-size: 0.9rem;
      font-weight: 500;
    }

    @media (max-width: 480px) {
      .login-container { margin: 1rem; }
      .login-header { height: 150px; padding: 1.5rem; }
      .login-form { padding: 1.5rem; }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h2>Welcome</h2>
      <p>Please enter your credentials to access your account</p>
    </div>

    <form class="login-form" action="login.php" method="POST">
      <div class="form-group">
        <label for="name">Username</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          class="form-control" 
          placeholder="Enter your username"
          required
        >
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          placeholder="Enter your password"
          required
        >
        <span class="password-toggle" onclick="togglePassword()">Show</span>
      </div>

      <button type="submit" class="btn" name="submit">Login</button>

      <div class="form-footer">
        Don't have an account? <a href="register.php">Register here</a>
      </div>

    <?php if (!empty($error)) : ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleBtn = document.querySelector('.password-toggle');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Hide';
      } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Show';
      }
    }
  </script>
</body>
</html>
