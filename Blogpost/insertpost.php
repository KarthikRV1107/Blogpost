<?php
session_start();
include "db.php";

$errors = [];
$success = '';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$max_file_size = 5 * 1024 * 1024;

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
            .denied-container {
                text-align: center;
                padding: 2rem;
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                max-width: 500px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 1rem;
            }
            p {
                margin-bottom: 1.5rem;
                color: #6c757d;
            }
            a {
                color: #0d6efd;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="denied-container">
            <h1><i class="fas fa-ban"></i> Access Denied</h1>
            <p>Your role ('.htmlspecialchars($_SESSION['user_role'] ?? 'unknown').') does not have permission to view this page.</p>
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Return to Dashboard</a>
        </div>
    </body>
    </html>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (strlen($title) > 255) {
        $errors['title'] = "Title cannot exceed 255 characters";
    }

    $image_path = null;
    if (!empty($_FILES['post_image']['name'])) {
        $file_name = $_FILES['post_image']['name'];
        $file_tmp = $_FILES['post_image']['tmp_name'];
        $file_size = $_FILES['post_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            $errors['image'] = "Only JPG, JPEG, PNG & GIF files are allowed";
        } elseif ($file_size > $max_file_size) {
            $errors['image'] = "File size must be less than 5MB";
        } else {
            $unique_name = uniqid('post_', true) . '.' . $file_ext;
            $upload_path = 'uploads/' . $unique_name;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_path = $upload_path;
            } else {
                $errors['image'] = "Failed to upload image";
            }
        }
    }

    if (empty($errors)) {
        $insertSql = "INSERT INTO posts (title, content, user_id, image_path) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ssis", $title, $content, $user_id, $image_path);

        if ($insertStmt->execute()) {
            $success = "Post created successfully!";
            $title = $content = '';
        } else {
            if ($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            $errors['database'] = "Error creating post: " . $conn->error;
        }
        $insertStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .container {
            max-width: 800px;
        }
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .image-preview {
            max-width: 100%;
            max-height: 300px;
            margin-top: 10px;
            display: none;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .upload-area:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h2 class="text-center mb-4">Create Post</h2>
            <form action="insertpost.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                              id="content" name="content" rows="5"><?= htmlspecialchars($content ?? '') ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="invalid-feedback"><?= $errors['content'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="post_image" class="form-label">Post Image (optional)</label>
                    
                    <div class="upload-area" onclick="document.getElementById('post_image').click()">
                        <input type="file" id="post_image" name="post_image" accept="image/*" style="display: none" onchange="previewImage(event)">
                        <div id="upload-text">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem"></i>
                            <p>Click to upload or drag and drop</p>
                            <p class="text-muted">Supports: JPG, JPEG, PNG, GIF (Max 5MB)</p>
                        </div>
                        <img id="image-preview" class="image-preview" alt="Preview">
                    </div>
                    
                    <?php if (isset($errors['image'])): ?>
                        <div class="text-danger"><?= $errors['image'] ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Post</button>
                <a href="displaypost.php" class="btn btn-secondary">Cancel</a><br><br>

                <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if (isset($errors['database'])): ?>
                    <div class="alert alert-danger"><?= $errors['database'] ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            const uploadText = document.getElementById('upload-text');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadText.style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
