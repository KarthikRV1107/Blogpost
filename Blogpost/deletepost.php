<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'author') {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                background: white;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                max-width: 500px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 1rem;
            }
            p {
                color: #555;
                margin-bottom: 1rem;
            }
            a {
                color: #0d6efd;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Access Denied</h1>
            <p>Your role ('.htmlspecialchars($_SESSION['user_role']).') does not have permission to delete posts.</p>
            <a href="dashboard.php">Return to Dashboard</a>
        </div>
    </body>
    </html>';
    exit();
}

if (!isset($_GET['post_id'])) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                background: white;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                max-width: 500px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 1rem;
            }
            p {
                color: #555;
                margin-bottom: 1rem;
            }
            a {
                color: #0d6efd;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Error</h1>
            <p>No post ID specified.</p>
            <a href="displaypost.php">Return to Posts</a>
        </div>
    </body>
    </html>';
    exit();
}

$post_id = intval($_GET['post_id']);
$user_id = $_SESSION['user_id'];

$checkSql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $post = $result->fetch_assoc();

    if (!empty($post['image_path']) && file_exists($post['image_path'])) {
        unlink($post['image_path']);
    }

    $deleteSql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $post_id, $user_id);

    if ($deleteStmt->execute()) {
        header("Location: displaypost.php?msg=Post+Deleted+Successfully");
        exit();
    } else {
        echo "Error deleting post: " . $conn->error;
    }

    $deleteStmt->close();
} else {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Post Not Found</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                background: white;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                max-width: 500px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 1rem;
            }
            p {
                color: #555;
                margin-bottom: 1rem;
            }
            a {
                color: #0d6efd;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Access Denied</h1>
            <p>You do not have permission to delete this post.</p>
            <a href="displaypost.php">Return to Posts</a>
        </div>
    </body>
    </html>';
}

$stmt->close();
$conn->close();
?>
