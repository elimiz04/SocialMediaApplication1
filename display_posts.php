<?php
session_start();
include("../includes/connection.php");
include("../includes/functions.php");

// Retrieve user's posts from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM posts WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user has set a color mode preference
if (!isset($_SESSION['color_mode'])) {
    // If not, set a default color mode (e.g., light mode)
    $_SESSION['color_mode'] = 'light';
}

// Function to apply the appropriate CSS class based on the color mode
function getColorModeClass() {
    return $_SESSION['color_mode'] === 'light' ? 'light-mode' : 'dark-mode';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Posts</title>
    <style>
        /* Add table styles here */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .image-container img {
            max-width: 100px; /* Adjust the size of images */
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body class="<?php echo getColorModeClass(); ?>">
    <h1>Your Posts</h1>
    
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($result && $result->num_rows > 0) {
                while ($post = $result->fetch_assoc()) {
                    // Assuming you have a 'title' column in the posts table.
                    // If not, you can either display 'content' or remove this column.
                    $title = isset($post['title']) ? htmlspecialchars($post['title']) : "No Title";

                    echo '<tr>';
                    echo '<td>' . $title . '</td>';
                    echo '<td>' . nl2br(htmlspecialchars($post['content'])) . '</td>';

                    // Check if the image exists
                    if (!empty($post['image_filename'])) {
                        echo '<td><div class="image-container"><img src="../uploads/' . htmlspecialchars($post['image_filename']) . '" alt="Post Image"></div></td>';
                    } else {
                        echo '<td>No Image</td>';
                    }
                    echo '</tr>';
                }
            } else {
                echo "<p>No posts found.</p>";
            }
        ?>
        </tbody>
    </table>

    <!-- Form to create a new post -->
    <form method="post" action="add_post.php" enctype="multipart/form-data">
        <textarea name="content" placeholder="Write a new post..."></textarea>
        <input type="file" name="image">
        <button type="submit">Post</button>
    </form>
</body>
</html>
