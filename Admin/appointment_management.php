<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings for "Requests" tab
$sql = "SELECT b.*, u.first_name, u.last_name 
        FROM Bookings b
        JOIN Users u ON b.umak_email = u.umak_email
        WHERE b.status = 'Pending'";

$sql2 = "SELECT b.*, u.first_name, u.last_name 
        FROM Bookings b
        JOIN Users u ON b.umak_email = u.umak_email
        WHERE b.status = 'Approved'";

$resultPending = $conn->query($sql);

$resultApproved = $conn->query($sql2);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management</title>
    <link rel="icon" type="image/x-icon" href="../MDO/mdo_logo_circle.png">
    <style>
        body {
            background: #618DC2;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header styles */
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

        .header .text-container {
            color: white;
            font-size: 14px;
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

        /* Tabs container */
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

        /* Main content area */
        .content {
            padding: 20px;
            background-color: #618DC2;
            min-height: calc(100vh - 140px); /* Adjust based on header and tabs height */
            display: none;
        }

        .content.active {
            display: block;
        }

        /* Appointment cards */
        .appointment-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .appointment-details {
            font-size: 14px;
            line-height: 1.5;
        }

        .appointment-actions {
            display: flex;
            gap: 10px;
        }

        .appointment-actions button {
            border: none;
            background-color: #0A1128;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .appointment-actions button:hover {
            background-color: #1E3A8A;
        }

        .appointment-actions .reject-btn {
            background-color: #D72638;
        }

        .appointment-actions .reject-btn:hover {
            background-color: #A01E2A;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }

        .modal-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin: 10px auto;
        }

        .modal-actions button {
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-actions .cancel-btn {
            background-color: #D72638;
            color: white;
        }

        .modal-actions .cancel-btn:hover {
            background-color: #A01E2A;
        }

        .modal-actions .close-btn {
            background-color: #0A1128;
            color: white;
        }

        .modal-actions .close-btn:hover {
            background-color: #1E3A8A;
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
            <div class="text-container">Welcome, Admin!</div>
        </div>
        <div class="dashboard-title">Appointment Management</div>
        <div class="nav-icons">
            <a href="user_management.php" title="User Management">
                <img src="../MDO/twopeople.png" alt="User Management">
            </a>
            <a href="appointment_management.php" title="Appointment Management">
                <img src="../MDO/user_journal_yellow.png" alt="Appointment Management">
            </a>
            <a href="content_manager.php" title="Content Manager">
                <img src="../MDO/edit_white.png" alt="Content Manager">
            </a>
            <a href="admin_profile.php" title="Admin Profile">
                <img src="../MDO/profile.png" alt="Admin Profile">
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" data-tab="requests">Requests</div>
        <div class="tab" data-tab="confirmed">Confirmed</div>
        <div class="tab" data-tab="reports">Reports</div>
    </div>

    <!-- Requests Tab -->
    <div class="content active" id="requests">
        <?php if ($resultPending->num_rows > 0): ?>
            <?php while ($row = $resultPending->fetch_assoc()): ?>
                <div class="appointment-card" data-id="<?php echo $row['booking_id']; ?>">
                <div class="appointment-details">
                    <strong>Requested By:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?><br>
                    <strong>Email: </strong> <?= htmlspecialchars($row['umak_email']) ?><br>
                    <strong>Service:</strong> <?= htmlspecialchars($row['service']) ?><br>
                    <strong>Service:</strong> <?= htmlspecialchars($row['service_type']) ?><br>
                    <strong>Date:</strong> <?= htmlspecialchars($row['booking_date']) ?><br>
                    <strong>Time:</strong> <?= htmlspecialchars($row['booking_time']) ?><br>
                    <strong>Remarks:</strong> <?= htmlspecialchars($row['remarks']) ?>
                    </div>
                        <div class="appointment-actions">
                        <button class="accept-btn">✓</button>
                        <button class="reject-btn">✕</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No booking requests available.</p>
        <?php endif; ?>
    </div>

    
    <!-- Confirmed Tab -->
    <div class="content"id="confirmed">
        <?php if ($resultApproved->num_rows > 0): ?>
            <?php while ($row = $resultApproved->fetch_assoc()): ?>
                <div class="appointment-card" data-id="<?php echo $row['booking_id']; ?>">
                <div class="appointment-details">
                    <strong>Requested By:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?><br>
                    <strong>Email: </strong> <?= htmlspecialchars($row['umak_email']) ?><br>
                    <strong>Service:</strong> <?= htmlspecialchars($row['service']) ?><br>
                    <strong>Service:</strong> <?= htmlspecialchars($row['service_type']) ?><br>
                    <strong>Date:</strong> <?= htmlspecialchars($row['booking_date']) ?><br>
                    <strong>Time:</strong> <?= htmlspecialchars($row['booking_time']) ?><br>
                    <strong>Remarks:</strong> <?= htmlspecialchars($row['remarks']) ?>
                    </div>
                    <div class="appointment-actions">
                        <button class="reject-btn">✕</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No booking requests available.</p>
        <?php endif; ?>
    </div>

    <!-- Reports Tab -->
    <div class="content" id="reports">
        <p>No reports available.</p>
    </div>


    <!-- Confirmation Modal -->
<div id="confirmation-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            Are you sure you want to update this booking?
        </div>
        <div class="modal-actions">
            <button id="confirm-btn" class="confirm-btn">Yes</button>
            <button id="cancel-btn" class="cancel-btn">No</button>
        </div>
    </div>
</div>
<script>
    // Get the modal and buttons
    const modal = document.getElementById('confirmation-modal');
    const confirmBtn = document.getElementById('confirm-btn');
    const cancelBtn = document.getElementById('cancel-btn');

    let actionToPerform = null;  // Variable to store the action to be performed
    let cardToUpdate = null;     // Store the card that needs to be updated

    // Handle Accept Button Click
    document.querySelectorAll('.accept-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Get the closest card and booking ID
            cardToUpdate = this.closest('.appointment-card');
            const bookingId = cardToUpdate.getAttribute('data-id');

            // Set the action to "accept"
            actionToPerform = 'accept';

            // Show the confirmation modal
            modal.style.display = 'flex';
        });
    });

    // Handle Reject Button Click
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Get the closest card and booking ID
            cardToUpdate = this.closest('.appointment-card');
            const bookingId = cardToUpdate.getAttribute('data-id');

            // Set the action to "reject"
            actionToPerform = 'reject';

            // Show the confirmation modal
            modal.style.display = 'flex';
        });
    });

    // Confirm Action (Accept or Reject)
    confirmBtn.addEventListener('click', function () {
        if (cardToUpdate && actionToPerform) {
            fetch('update_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: cardToUpdate.getAttribute('data-id'), action: actionToPerform })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI based on action
                    if (actionToPerform === 'accept') {
                        const confirmedTab = document.getElementById('confirmed');
                        confirmedTab.insertAdjacentHTML('beforeend', cardToUpdate.outerHTML);
                        cardToUpdate.remove();
                        document.querySelector('.empty-message').style.display = 'none';
                    } else if (actionToPerform === 'reject') {
                        cardToUpdate.remove();
                    }
                } else {
                    alert('Failed to update booking.');
                }
            });
        }

        // Close the modal after action
        modal.style.display = 'none';
        actionToPerform = null;
        cardToUpdate = null;
    });

    // Cancel Action (Close Modal)
    cancelBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        actionToPerform = null;
        cardToUpdate = null;
    });

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            actionToPerform = null;
            cardToUpdate = null;
        }
    }
</script>


    <!-- JavaScript -->
    <script>
        // Tab switching
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

        // Activate the clicked tab and its corresponding content
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
        });
        });

        // Handle moving to the Confirmed tab
        const acceptButtons = document.querySelectorAll('.accept-btn');        
        const emptyMessage = document.querySelector('.empty-message');

        acceptButtons.forEach(button => {
        button.addEventListener('click', (e) => {
        const appointmentCard = e.target.closest('.appointment-card');

        // Replace buttons with "Reschedule" and "Cancel"
        const actions = clone.querySelector('.appointment-actions');
        actions.innerHTML = `
            <img src="../MDO/clock.png" alt="Reschedule" class="reschedule-btn" onclick="openModal('rescheduleModal')">
            <img src="../MDO/delete.png" alt="Delete" class="cancel-btn" onclick="openModal('cancelModal')">
        `;  

        // Remove the original card from Requests
        appointmentCard.remove();

        // Remove the "No confirmed appointments yet" message if it exists
        if (emptyMessage) {
            emptyMessage.style.display = 'none';
        }
        });
        });

        // Modal functions
        function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>