<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_username = $_SESSION['admin_username'];
$sql = "SELECT username, email FROM Admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
$stmt->fetch();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        // If password is provided, hash it before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE Admins SET username = ?, email = ?, password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $admin_username);
    } else {
        $sql = "UPDATE Admins SET username = ?, email = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $admin_username);
    }

    if ($stmt->execute()) {
        $_SESSION['admin_username'] = $username; // Update session username
        echo "<script>alert('Profile updated successfully!'); window.location.href='admin_profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #618DC2;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background-color: #0A1128;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .header .logo {
            display: flex;
            align-items: center;
        }

        .header .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .header .dashboard-title {
            font-size: 18px;
            font-weight: bold;
        }

        .header .nav-icons {
            display: flex;
            gap: 15px;
        }

        .header .nav-icons a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #1E3A8A;
            transition: background-color 0.3s;
        }

        .header .nav-icons a:hover {
            background-color: #314e9c;
        }

        .header .nav-icons img {
            width: 20px;
            height: 20px;
        }

        /* Profile Section */
        .profile-section {
            margin: 20px;
            background-color: #E2E8F0;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile-section .details {
            display: flex;
            align-items: center;
        }

        .profile-section img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .profile-section .info {
            display: flex;
            flex-direction: column;
        }

        .profile-section .edit-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .profile-section .edit-btn img {
            width: 20px;
            height: 20px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-content input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-content .modal-buttons button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content .modal-buttons .save-btn {
            background-color: #4CAF50;
            color: white;
        }

        .modal-content .modal-buttons .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        /* Logout Button */
        .logout-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #d7372f;
        }


         .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color:black;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            background-color:#F5EC3A;
            float:right;
        }
        .back-link-container {
            position: fixed;
            bottom: 70px; /* Distance from the bottom */
            right: 20px; /* Distance from the right */
            z-index: 10; /* Ensures it stays on top */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="../MDO/umaklogo.png" alt="Logo">
            <div class="text-container">
            <span>Welcome, <?php echo htmlspecialchars($current_username); ?>!</span>
            </div>
        </div>
        <div class="dashboard-title">
            <span>Admin Profile</span>
        </div>
        <div class="nav-icons">
            <a href="user_management.php" title="User Management">
                <img src="../MDO/twopeople.png" alt="User Management">
            </a>
            <a href="appointment_management.php#requests" title="Appointment Management">
                <img src="../MDO/user_journal.png" alt="Appointment Management">
            </a>
            <a href="content_manager.php#chat" title="Content Manager">
                <img src="../MDO/edit_white.png" alt="Content Manager">
            </a>
            <a href="admin_profile.php" title="Admin Profile">
                <img src="../MDO/profile_yellow.png" alt="Admin Profile">
            </a>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
        <div class="details">
            <img src="../MDO/ayase.jpg" alt="Admin Profile Picture">
            <div class="info">
                <span><strong><?php echo htmlspecialchars($current_username); ?></strong></span>
                <span><?php echo htmlspecialchars($current_email); ?></span>
            </div>
        </div>
        <button class="edit-btn" onclick="openModal()">
            <img src="../MDO/edit.png" alt="Edit">
        </button>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Profile</h2>
            <form method="POST" action="admin_profile.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter username" value="<?php echo htmlspecialchars($current_username); ?>">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo htmlspecialchars($current_email); ?>">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password">
                <div class="modal-buttons">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logout Button -->
    <button class="logout-btn" onclick="window.location.href='logout.php'">Log Out</button>
    <div class="back-link-container">
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    <script>
        function openModal() {
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function saveChanges() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            // Add your save logic here
            alert(`Saved changes:\nUsername: ${username}\nEmail: ${email}`);
            closeModal();
        }
    </script>
</body>
</html>