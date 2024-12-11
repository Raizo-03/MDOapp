<?php
require dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $status = $_POST['status'];

    // Fetch old user details including email
    $oldDetailsQuery = $conn->prepare("SELECT umak_email, status FROM Users WHERE user_id = ?");
    $oldDetailsQuery->bind_param("i", $userId);
    $oldDetailsQuery->execute();
    $oldDetails = $oldDetailsQuery->get_result()->fetch_assoc();
    $umakEmail = $oldDetails['umak_email'];

    if (!$umakEmail) {
        echo json_encode(['status' => 'error', 'message' => 'User email not found']);
        exit();
    }

    // Update user details
    $updateQuery = $conn->prepare("UPDATE Users SET first_name = ?, last_name = ?, status = ? WHERE user_id = ?");
    $updateQuery->bind_param("sssi", $firstName, $lastName, $status, $userId);

    if ($updateQuery->execute()) {
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true; 
            $mail->Username = 'umakmdo@gmail.com'; 
            $mail->Password = 'jhdp unfj togy qbxf'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('ebuscato.k12043456@umak.edu.ph', 'MDO App');
            $mail->addAddress($umakEmail);

            // Check if the status has changed and set the appropriate email message
            if ($oldDetails['status'] !== $status) {
                $statusMessage = $status === 'active' ? 'Your account has been reactivated.' : 'Your account has been deactivated.';
                $mail->Subject = 'Account Status Changed';
                $mail->Body = "Dear $firstName $lastName,<br><br>$statusMessage<br><br>Regards,<br>MDOApp Admin";
            } else {
                $mail->Subject = 'Account Details Updated';
                $mail->Body = "Dear $firstName $lastName,<br><br>Your account details have been successfully updated.<br><br>Regards,<br>MDOApp Admin";
            }

            $mail->isHTML(true);
            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }

        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
    }

    $updateQuery->close();
    $oldDetailsQuery->close();
    $conn->close();
    exit();
}
// Sort logic for Active Users
$sortActive = $_GET['sort_active'] ?? ''; // Default is no sort
$orderByActive = ($sortActive === 'name') ? "last_name, first_name" : "created_at";
$toggleSortUrlActive = ($sortActive === 'name') ? "?sort_active=none" : "?sort_active=name";
$buttonTextActive = ($sortActive === 'name') ? "Unsort Active" : "Sort Active by: Name";

// Sort logic for Inactive Users
$sortInactive = $_GET['sort_inactive'] ?? ''; // Default is no sort
$orderByInactive = ($sortInactive === 'name') ? "last_name, first_name" : "created_at";
$toggleSortUrlInactive = ($sortInactive === 'name') ? "?tab=inactive&sort_inactive=none" : "?tab=inactive&sort_inactive=name";
$buttonTextInactive = ($sortInactive === 'name') ? "Unsort Inactive" : "Sort Inactive by: Name";

// Query to fetch active users
$sqlActive = "
    SELECT u.user_id, u.student_id, u.umak_email, u.first_name, u.last_name, u.created_at,
           COUNT(b.booking_id) AS no_show_count
    FROM Users u
    LEFT JOIN Bookings b ON u.umak_email = b.umak_email AND b.status = 'No Show'
    WHERE u.status = 'active'
    GROUP BY u.user_id
    ORDER BY $orderByActive";


$resultActive = $conn->query($sqlActive);

// Query to fetch inactive users
$sqlInactive = "
    SELECT u.user_id, u.student_id, u.umak_email, u.first_name, u.last_name, u.created_at,
           COUNT(b.booking_id) AS no_show_count
    FROM Users u
    LEFT JOIN Bookings b ON u.umak_email = b.umak_email AND b.status = 'No Show'
    WHERE u.status = 'inactive'
    GROUP BY u.user_id
    ORDER BY $orderByInactive";

$resultInactive = $conn->query($sqlInactive);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="icon" type="image/x-icon" href="../MDO/mdo_logo_circle.png">
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #618DC2;
            margin: 0;
            padding: 0;
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
        /* header title */
        .users-header {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .users-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .users-header .sort-button div {
            padding: 10px 15px;
            border: none;
            background-color: #0A1128;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            
        }
        .users-header .sort-button a{
            text-decoration: none;
        }
        /* Tabs */
        .tabs {
            display: flex;
            background-color: #E0E7FF;
            border-bottom: 2px solid #0A1128;
        }

        .tab {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            flex: 1;
            text-align: center;
            background-color: #E0E7FF;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: white;
            border-bottom: 3px solid #0A1128;
        }

        /* Content */
        .content {
            padding: 20px;
            background-color: #618DC2;
            min-height: calc(100vh - 140px); /* Adjust based on header and tabs height */
            display: none;
        }

        .content.active {
            display: block;
        }

        /* User Cards */
        .user-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-card .actions{
            cursor: pointer;
        }

        .user-details {
            font-size: 14px;
            line-height: 1.5;
        }

        .user-actions {
            display: flex;
            gap: 10px;
        }

        .user-actions img {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: black;
            background-color: #007BFF;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            background-color:#F5EC3A;
            float:right;
        }

        .back-link-container {
            position: fixed;
            bottom: 20px; /* Distance from the bottom */
            right: 20px; /* Distance from the right */
            z-index: 10; /* Ensures it stays on top */
        }
       /* Modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; /* Ensure it appears above other elements */
            width: 450px; /* Adjust width as needed */
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content form {
            display: flex;
            flex-direction: column; /* Stack form fields vertically */
            gap: 15px; /* Add spacing between each form group */
        }

        .modal-content div {
            display: flex;
            flex-direction: column; 
            gap: 5px; 
        }

        .modal-content label {
            text-align: left; /* Align the label text to the left */
            align-self: flex-start
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%; /* Full width for inputs and textareas */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Include padding in width */
        }

        .modal-content textarea {
            resize: vertical; /* Allow vertical resizing only */
            height: 60px;
        }
        .modal .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 18px;
            cursor: pointer;
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
     
        /* Toggle switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            margin: 10px auto; 
            
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
        /* Gray Button */
        .btn-gray {
            background-color: #d3d3d3; /* Light gray */
            color: #333; /* Darker text */
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-transform: uppercase;
            text-align: center;
        }

        .btn-gray:hover {
            background-color: #c0c0c0; /* Darker gray on hover */
        }
        .warning-icon {
            width: 20px;
            height: 20px;
            margin-left: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <a href="dashboard.php" title="Dashboard">
            <img src="../MDO/umaklogo.png" alt="Logo">
            </a>
            <div class="text-container">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</span>
            </div>
        </div>
        <div class="dashboard-title">
            <span>User Management</span>
        </div>
        <div class="nav-icons">
            <a href="user_management.php" title="User Management">
                <img src="../MDO/twopeople_yellow.png" alt="User Management">
            </a>
            <a href="appointment_management.php#requests" title="Appointment Management">
                <img src="../MDO/user_journal.png" alt="Appointment Management">
            </a>
            <a href="content_manager.php#chat" title="Content Manager">
                <img src="../MDO/edit_white.png" alt="Content Manager">
            </a>
            <a href="admin_profile.php" title="Admin Profile">
                <img src="../MDO/profile.png" alt="Admin Profile">
            </a>
        </div>
    </div>

    <!-- active inactive -->
    <div class="tabs">
    <div class="tab active" data-tab="active">Active Users</div>
    <div class="tab" data-tab="inactive">Inactive Users</div>
</div>

 <!-- Active Users -->
 <div id="active" class="content active">
        <div class="users-header">
        <h2>Users</h2>
        <div class="sort-button">
            <a href="<?php echo $toggleSortUrlActive; ?>"><div><?php echo $buttonTextActive; ?></div></a>
        </div>
        </div>
        
        <div class="user-list">
        <?php while ($row = $resultActive->fetch_assoc()): ?>
        <div class="user-card">
            <h3>
                <?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?>
                <?php if ($row['no_show_count'] > 0): ?>
                    <img src="../MDO/warning.png" alt="No Show Warning" title="This user has <?= $row['no_show_count'] ?> no-show booking(s)." class="warning-icon">
                <?php endif; ?>
            </h3>
            <div class="actions">
                <img src="../MDO/edit.png" alt="Edit" class="openEditModalBtn" data-id="<?= htmlspecialchars($row['user_id']) ?>">
                <img src="../MDO/adjust.png" alt="Settings" class="openModalBtn" data-id="<?= htmlspecialchars($row['user_id']) ?>" data-status="inactive">
            </div>
        </div>
    <?php endwhile; ?>
        </div>
    </div>

 <!-- Inactive Users -->
 <div id="inactive" class="content">
        <div class="users-header">
        <h2>Users</h2>
        
        <div class="sort-button">
            <a href="<?php echo $toggleSortUrlInactive; ?>"><div><?php echo $buttonTextInactive; ?></div></a>
        </div>
        </div>
        <div class="user-list">
            <?php while ($row = $resultInactive->fetch_assoc()): ?>
                <div class="user-card">
                <h3>
                    <?= htmlspecialchars($row['last_name']) ?>, <?= htmlspecialchars($row['first_name']) ?>
                    <?php if ($row['no_show_count'] > 0): ?>
                    <img src="../MDO/warning.png" alt="No Show Warning" title="This user has <?= $row['no_show_count'] ?> no-show booking(s)." class="warning-icon">
                    <?php endif; ?>
                    </h3>
                    <div class="actions">
                        <img src="../MDO/edit.png" alt="Edit" class="openEditModalBtn" data-id="<?= htmlspecialchars($row['user_id']) ?>">
                        <img src="../MDO/adjust.png" alt="Settings" class="openModalBtn" data-id="<?= $row['user_id'] ?>" data-status="active">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Main Content -->
    <div class="container">
         <!-- <div class="user-list">
            <?php
            ?> 
        </div> -->
    </div>

        <!-- Status Modal -->
        <div id="statusModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <p>Change user status:</p>
                <p>On = Active(Default) | Off = Inactive</p>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <label class="switch">
                        <input type="checkbox" id="statusToggle">
                        <span class="slider"></span>
                    </label>
                    <button class="btn-gray" onclick="updateStatus()">Confirm</button>
                </div>
            </div>
        </div>

        <div class="modal-backdrop" id="modalBackdrop"></div>

        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" id="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit User Details</h2>
                <form id="editForm">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div>
                        <label>First Name:</label>
                        <input type="text" id="editFirstName" name="first_name" required>
                    </div>
                    <div>
                        <label>Last Name:</label>
                        <input type="text" id="editLastName" name="last_name" required>
                    </div>
                    <div>
                        <label>Password:</label>
                        <input type="text" id="editPassword" name="password">
                    </div>
                    <div>
                        <label>Student ID:</label>
                        <input type="text" id="editStudentId" name="student_id" required>
                    </div>
                    <div>
                        <label>Contact No.:</label>
                        <input type="text" id="editContactNumber" name="contact_number">
                    </div>
                    <div>
                        <label>Address:</label>
                        <textarea id="editAddress" name="address"></textarea>
                    </div>
                    <div>
                        <label>Guardian Contact No.:</label>
                        <input type="text" id="editGuardianContact" name="guardian_contact_number">
                    </div>
                    <div>
                        <label>Guardian Address:</label>
                        <textarea id="editGuardianAddress" name="guardian_address"></textarea>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="save-btn" onclick="saveUserData()">Save</button>
                        <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-backdrop" id="editModalBackdrop"></div>

        <!-- Back Link -->
        <div class="back-link-container">
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    <script>
    // tabs
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.content');

        const activateTabFromHash = () => {
        const hash = window.location.hash.substring(1) || 'active'; // Default to 'active'
        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));
        const activeTab = document.querySelector(`.tab[data-tab="${hash}"]`);
        const activeContent = document.getElementById(hash);
        if (activeTab && activeContent) {
            activeTab.classList.add('active');
            activeContent.classList.add('active');
        }
    };
    
    tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
                const target = tab.dataset.tab;
                window.location.hash = target; // Update URL hash
                activateTabFromHash();
            });
        });
          // Handle page load and hash change
    window.addEventListener('hashchange', activateTabFromHash);
    activateTabFromHash();
    
    // sort
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'active'; // Default to 'active' if no tab is specified

    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    contents.forEach(content => {
        content.classList.remove('active');
    });

// Activate the correct tab
document.querySelector(`.tab[data-tab="${activeTab}"]`).classList.add('active');
document.getElementById(activeTab).classList.add('active');
    
    //modal
    let selectedUserId;
    let currentStatus;

    document.querySelectorAll('.openModalBtn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const userId = e.target.getAttribute('data-id');
            const status = e.target.getAttribute('data-status');
            openModal(userId, status);
        });
    });
    
    function openModal(userId, status) {
    selectedUserId = userId;
    currentStatus = status;

    // Set toggle position based on the current status
    const statusToggle = document.getElementById('statusToggle');
    statusToggle.checked = (status === 'inactive'); // Fix: Checked when inactive, unchecked otherwise

    // Show the modal and backdrop
    document.getElementById('statusModal').style.display = 'block';
    document.getElementById('modalBackdrop').style.display = 'block';
}

    function closeModal() {
        document.getElementById('statusModal').style.display = 'none';
        document.getElementById('modalBackdrop').style.display = 'none';
    }

    function updateStatus() {
    const newStatus = document.getElementById('statusToggle').checked ? 'active' : 'inactive';

    fetch('update_user_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: selectedUserId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`User status updated to ${newStatus}!`);
            location.reload(); // Refresh the page to reflect changes
        } else {
            console.error(data.error); // Log the error if any
            alert('Error updating status: ' + (data.error || 'Unknown error.'));
        }
        closeModal();
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An unexpected error occurred.');
    });
}

// Add event listener for the edit buttons to open the modal
document.addEventListener("DOMContentLoaded", () => {
    // Ensure the buttons for opening the Edit Modal are correctly targeted
    const editButtons = document.querySelectorAll('.openEditModalBtn');

    editButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const userId = e.target.getAttribute('data-id'); // Get user ID
            openEditModal(userId); // Open Edit Modal
        });
    });

    function openEditModal(userId) {
    selectedUserId = userId;

    // Use fetch_user_details.php to get user data
    fetch(`fetch_user_details.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Populate the modal fields
            document.getElementById('editUserId').value = data.user_id;
            document.getElementById('editFirstName').value = data.first_name;
            document.getElementById('editLastName').value = data.last_name;
            document.getElementById('editPassword').value = '';
            document.getElementById('editStudentId').value = data.student_id;
            document.getElementById('editContactNumber').value = data.contact_number || '';
            document.getElementById('editAddress').value = data.address || '';
            document.getElementById('editGuardianContact').value = data.guardian_contact_number || '';
            document.getElementById('editGuardianAddress').value = data.guardian_address || '';
            // Display the modal
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('editModalBackdrop').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching user data:', error);
            alert('An unexpected error occurred.');
        });
}

    // Function to close the Edit Modal
    function closeEditModal() {
    document.getElementById('editModal').style.display = 'none'; // Hide modal
    document.getElementById('editModalBackdrop').style.display = 'none'; // Hide backdrop
}


    document.getElementById('close').addEventListener('click', closeEditModal);
    // Event listener to close the modal when the close button is clicked
    document.getElementById('editModalBackdrop').addEventListener('click', closeEditModal);

function saveUserData() {
    const updatedData = {
        user_id: selectedUserId,
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        password: document.getElementById('editPassword').value ? document.getElementById('editPassword').value : null,
        student_id: document.getElementById('editStudentId').value,
        contact_number: document.getElementById('editContactNumber').value,
        address: document.getElementById('editAddress').value,
        guardian_contact_number: document.getElementById('editGuardianContact').value,
        guardian_address: document.getElementById('editGuardianAddress').value,
        profile_image: document.getElementById('editProfileImage').value
    };

    console.log('Data to be saved:', updatedData);  // Log data to the console

    fetch('update_user_details.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Server Response:', data); // Log the server response
        if (data.success) {
            alert('User data updated successfully!');
            location.reload(); // Reload the page to reflect changes
        } else {
            alert('Error updating user data: ' + (data.error || 'Unknown error.'));
        }
        closeEditModal(); // Close the modal
    })
    .catch(error => {
        console.error('Error updating user data:', error);
        alert('An unexpected error occurred.');
    });
}
    // Event listener to save changes when the "Save" button is clicked
    document.querySelector('.save-btn').addEventListener('click', saveUserData);
    document.querySelector('.cancel-btn').addEventListener('click', closeEditModal);
});
</script>
</body>
</html>

<?php
$conn->close();
?>