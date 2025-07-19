<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary-color: #6366f1;
      --primary-dark: #4f46e5;
      --secondary-color: #f43f5e;
      --background: #f1f5f9;
      --light: #fff;
      --dark: #1e293b;
      --gray: #64748b;
      --border-radius: 10px;
      --shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--background);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      max-width: 750px;
      width: 100%;
      background: var(--light);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: #fff;
      padding: 1.5rem 2rem;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 1.6rem;
      line-height: 1.4;
    }

    .content {
      padding: 2rem;
    }

    .content p {
      font-size: 1rem;
      color: var(--gray);
      text-align: center;
      margin-bottom: 2rem;
    }

    .button-group {
      display: flex;
      justify-content: flex-start;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .bottom-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn {
      padding: 0.55rem 1.3rem;
      font-size: 0.9rem;
      font-weight: 500;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      display: inline-block;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-profile,
    .btn-notify,
    .btn-display {
      background: #e0f2fe;
      color: #0f172a;
    }

    .btn-profile:hover,
    .btn-notify:hover,
    .btn-display:hover {
      background: #bae6fd;
    }

    .btn-back {
      background: #cbd5e1;
      color: #0f172a;
    }

    .btn-back:hover {
      background: #94a3b8;
    }

    .btn-logout {
      background: var(--secondary-color);
      color: #fff;
    }

    .btn-logout:hover {
      background: #e11d48;
    }

    @media (max-width: 600px) {
      .button-group,
      .bottom-buttons {
        flex-direction: column;
        align-items: stretch;
      }

      .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Welcome To Dashboard <br><?php echo htmlspecialchars($user_name); ?>.</h1>
    </div>

    <div class="content">
      <p>Role: <strong><?php echo htmlspecialchars(ucfirst($user_role)); ?></strong></p>

      <div class="button-group">
        <a class="btn btn-profile" href="#">Profile</a>
        <a class="btn btn-notify" href="#">Notifications</a>
        <a class="btn btn-display" href="displaypost.php">Display Posts</a>
      </div>

      <div class="bottom-buttons">
        <a class="btn btn-back" href="blogpost.php">Back</a>
        <a class="btn btn-logout" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
