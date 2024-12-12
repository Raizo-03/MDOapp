<?php
session_start();

// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1);

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $admin_username = $_POST['username'];
        $admin_password = $_POST['password'];

        if (empty($admin_username) || empty($admin_password)) {
            $message = "Username and password must be provided.";
        } else {
            $sql = "SELECT * FROM Admins WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $admin_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                if (password_verify($admin_password, $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid username or password.";
                }
            } else {
                $message = "Admin not found.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['forgot_password'])) {
        $message = "Not Available as of the moment. Please contact service provider.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* ...Existing Styles... */
        .message {
            color: black;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section -->
        <div class="logo-section">
            <img src="../MDO/mdo_logo.png" alt="University Logo">
        </div>
        <!-- Right Section -->
        <div class="form-section">
            <div class="login-form">
                <form method="POST" action="admin_login.php">
                    <!-- Message Display -->
                    <?php if (!empty($message)): ?>
                        <div class="message"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    <input type="text" name="username" placeholder="Enter username" required>
                    <input type="password" name="password" placeholder="Enter password" required>
                    <button type="submit">LOG IN</button>
                </form>
                <div class="forgot-password">
                    <form method="POST" action="admin_login.php">
                        <button type="submit" name="forgot_password" style="background: none; border: none; color: #ccc; cursor: pointer;">Forgot Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
