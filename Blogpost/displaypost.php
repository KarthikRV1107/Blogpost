<?php
ob_start();
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'author') {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Access Denied</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f1f5f9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .denied-container {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                text-align: center;
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
                color: #6366f1;
                text-decoration: none;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="denied-container">
            <h1><i class="fas fa-ban"></i> Access Denied</h1>
            <p>Your role (' . htmlspecialchars($_SESSION['user_role']) . ') does not have permission to view this page.</p>
            <a href="dashboard.php"><i class="fas"></i> Back to Dashboard</a>
        </div>
    </body>
    </html>';
    exit();
}

$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$query = "SELECT posts.*, users.name AS author_name 
          FROM posts 
          JOIN users ON posts.user_id = users.id";

if (!empty($search)) {
    $query .= " WHERE posts.title LIKE ? OR posts.content LIKE ?";
}
$query .= " ORDER BY posts.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$total_posts = $result->num_rows;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Posts Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Posts Management</h1>
      <div class="flex space-x-2">
        <form method="GET" class="flex">
          <input type="text" name="search" placeholder="Search posts..."
            class="px-2 py-2 border rounded-l-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
            value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg">
            Search
          </button>
        </form>
        <a href="insertpost.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
         New Post
        </a>
        <a href="dashboard.php" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Dashboard</a>
      </div>
    </div>

    <div class="space-y-6">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col p-6">

          <div class="flex justify-between items-start mb-4">
            <div class="flex flex-col max-w-3xl">
              <h2 class="text-xl font-bold text-gray-800 mb-2">
                <?php echo htmlspecialchars($row['title']); ?>
              </h2>
              <p class="text-gray-600 text-sm whitespace-pre-line">
                <?php echo nl2br(htmlspecialchars($row['content'])); ?>
              </p>
            </div>
            <div class="flex space-x-2">
              <a href="updatepost.php?post_id=<?php echo $row['id']; ?>"
                 class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-200 text-sm">
                Edit
              </a>
              <a href="deletepost.php?post_id=<?php echo $row['id']; ?>"
                 class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-200 text-sm">
                Delete
              </a>
            </div>
          </div>

          <div class="flex">
            <img src="<?php 
              echo isset($row['image_path']) && !empty($row['image_path']) 
                ? htmlspecialchars($row['image_path']) 
                : 'https://placehold.co/600x300?text=' . urlencode($row['title']); 
            ?>" 
            alt="<?php echo htmlspecialchars($row['title']); ?>"
            class="w-full md:w-1/2 h-100 object-cover rounded-lg"/>
          </div><br>
          <p class="text-gray-500 text-xs mt-2">
            Posted by <?php echo htmlspecialchars($row['author_name']); ?> 
            on <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?>
          </p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <p class="text-sm text-gray-400 text-center mt-4">Total Posts: <?php echo $total_posts; ?></p>

  <footer class="bg-gray-800 text-white py-4 mt-8">
    <div class="container mx-auto text-center">
      <p>&copy; <?php echo date("Y"); ?> Voltora. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>

<?php ob_end_flush(); ?>
