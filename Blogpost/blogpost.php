<?php
session_start();
include("db.php");

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$postsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $postsPerPage;

$countSql = "SELECT COUNT(*) as total FROM posts WHERE title LIKE ? OR content LIKE ?";
$countStmt = $conn->prepare($countSql);
$countParam = '%' . $search . '%';
$countStmt->bind_param("ss", $countParam, $countParam);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

$sql = "SELECT p.*, u.name AS author_name FROM posts p JOIN users u ON p.user_id = u.id WHERE p.title LIKE ? OR p.content LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $countParam, $countParam, $postsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAULTORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(187, 152, 202, 0.2);
            position: sticky;
            top: 0;
            margin-right: -20px;
            margin-left: -20px;
            z-index: 1000;
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
        }

        .navbar-brand img {
            height: 35px;
            margin-right: 8px;
        }

        .navbar-nav .nav-link {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .post-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .post-title {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .post-content {
            font-size: 1rem;
            color: #4a5568;
            margin-bottom: 15px;
        }
        .post-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .post-actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .post-actions a:hover {
            text-decoration: underline;
        }
        footer {
            background: linear-gradient(135deg, #6071b5, #3a558f);
            color: white;
            padding: 40px 20px;
            width: 100%;
            position: relative;
            margin-top: -33px;
            bottom: -19px;
        }
        footer a {
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="logo/Compny Logo.png">Vaultora</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Resources</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <li class="nav-item">
                        <span class="nav-link"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Sign In</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
            </ul>
        </div>
    </div>
</nav><br>

<div class="container">

    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search posts..." class="form-control" />
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if ($search && $result->num_rows === 0): ?>
        <div class="alert alert-warning" role="alert">
            No posts found matching your search criteria.
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post-container">
                <h2 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                <p class="post-content"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <?php if (!empty($row['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Post Image" class="post-image">
                <?php endif; ?>
                <div class="post-meta">
                    <span class="post-author">By <?= htmlspecialchars($row['author_name']) ?></span>
                    <span class="text-muted">•</span>
                    <span class="text-muted"><?= date('M j, Y', strtotime($row['created_at'])) ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage - 1; ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage + 1; ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center">
                <h4>Vaultora</h4>
                <p>Create your account and start trading!</p>
                <a href="register.php" class="btn btn-warning btn-lg">Start Now</a>
            </div>
            <div class="col-md-4">
                <h5>Services</h5>
                <ul class="list-unstyled">
                    <li><a href="#" style="color: white;">Pricing</a></li>
                    <li><a href="#" style="color: white;">Crypto Savings Plans</a></li>
                    <li><a href="#" style="color: white;">Security</a></li>
                    <li><a href="#" style="color: white;">Asset Classes</a></li>
                    <li><a href="#" style="color: white;">Help Articles</a></li>
                    <li><a href="#" style="color: white;">API</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Company</h5>
                <ul class="list-unstyled">
                    <li><a href="#" style="color: white;">About Us</a></li>
                    <li><a href="#" style="color: white;">Careers</a></li>
                </ul>
                <h5>Legal</h5>
                <ul class="list-unstyled">
                    <li><a href="#" style="color: white;">Terms and Conditions</a></li>
                    <li><a href="#" style="color: white;">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-4">
            <small>© All rights reserved</small>
            <p style="margin: 0; font-size: 12px;">Vaultora is a registered trademark by the company Vaultoro Limited, operating since 2005 and protecting our customers and their funds, together with our partners, complying with the current regulations on Prevention of Money Laundering and Prevention of Terrorist Financing.</p>
        </div>
        <div class="text-center mt-2">
            <a href="https://www.instagram.com/"><img src="logo/vt-icon-instagram.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
            <a href="https://www.facebook.com/"><img src="logo/vt-icon-facebook.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
            <a href="https://www.linkedin.com/"><img src="logo/vt-icon-linkedin.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
            <a href="https://x.com/?lang=en"><img src="logo/vt-icon-twitter.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
            <a href="https://www.reddit.com/"><img src="logo/vt-icon-reddit.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
            <a href="https://www.youtube.com/"><img src="logo/vt-icon-youtube.svg" alt="Social" style="width: 20px; margin-right: 10px;"></a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>