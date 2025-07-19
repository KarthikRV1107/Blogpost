<?php
session_start();
include "db.php";

$message = "";

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = $_POST['role'];

    $errors = [];

    if (strlen($password) < 6 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must be at least 6 chars with 1 uppercase, 1 lowercase & 1 number.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO USERS (name, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $hashedPassword, $role);

        if (!$stmt->execute()) {
            $message = "Error: {$stmt->error}";
        } else {
            header("Location: login.php");
            exit();
        }
        $stmt->close();
    } else {
        $message = $errors[0];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary-color: #6366f1;
      --primary-dark: #4f46e5;
      --gray-color: #64748b;
      --border-radius: 12px;
      --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --transition: all 0.3s ease;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f1f5f9;
      color: #1e293b;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      line-height: 1.5;
    }

    .register-container {
      width: 100%;
      max-width: 480px;
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      margin: 2rem;
    }

    .register-header {
      position: relative;
      height: 150px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 2rem;
      color: white;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .register-header::before {
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

    .register-header h2 {
      font-size: 1.75rem;
      font-weight: 700;
      position: relative;
      z-index: 1;
    }

    .register-header p {
      opacity: 0.9;
      position: relative;
      z-index: 1;
      font-size: 0.875rem;
    }

    .register-form {
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

    .btn:hover { background-color: var(--primary-dark); }

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

    .form-footer a:hover { text-decoration: underline; }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 70%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 0.85rem;
      color: var(--gray-color);
      user-select: none;
    }

    @media (max-width: 480px) {
      .register-container { margin: 1rem; }
      .register-header { height: 150px; padding: 1.5rem; }
      .register-form { padding: 1.5rem; }
    }
  </style>
</head>
<body>
  <div class="register-container">
    <div class="register-header">
      <h2>Create Account</h2>
      <p>Fill in the details to register</p>
    </div>

    <form class="register-form" action="" method="POST">
      <div class="form-group">
        <label for="name">Username</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter username" required>
      </div>

      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" class="form-control" required>
          <option value="subscriber">Subscriber</option>
          <option value="author">Author</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password">
        <span class="password-toggle" onclick="togglePassword('password', this)">Show</span>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm password">
        <span class="password-toggle" onclick="togglePassword('confirmPassword', this)">Show</span>
      </div>

      <button type="submit" name="submit" class="btn">Register</button>

      <div class="form-footer">
        Already have an account? <a href="login.php">Login here</a>
      </div>

      <?php if ($message): ?>
        <div style="color: red; text-align: center; margin-top: 1rem; font-size: 0.875rem;">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
    </form>
  </div>

  <script>
    function togglePassword(id, el) {
      const input = document.getElementById(id);
      if (input.type === "password") {
        input.type = "text";
        el.textContent = "Hide";
      } else {
        input.type = "password";
        el.textContent = "Show";
      }
    }
  </script>
</body>
</html>
