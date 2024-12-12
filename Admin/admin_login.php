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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #111C4E;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            background-color: #111C4E;
        }
        .logo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: white;
        }
        .logo-section img {
            width: 400px;
            height: auto;
            margin-bottom: 20px;
        }
        .logo-section h3 {
            margin: 10px 0;
        }
        .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0px;
        }
        .login-form {
        width: 100%;
        max-width: 400px;
        color: black;
        padding: 30px;
        border-radius: 10px;
        text-align: center; /* Add this to center the button */
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 75%;
            padding: 10px;
            margin: 10px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-form button {
            width: 100px;
            padding: 10px;
            background-color: #fecb00;
            color: #03194f;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px auto; /* Adjust horizontal centering */
            display: block; /* Ensure it's treated as a block element */
        }
        .login-form button:hover {
            background-color: #e2b300;
        }
        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }
        .forgot-password a {
            color: #ccc;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
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