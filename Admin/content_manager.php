
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Manager</title>
    <link rel="icon" type="image/x-icon" href="../MDO/mdo_logo_circle.png">
    <style>
    /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #618DC2;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
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
            flex: 1;
            text-align: center;
            background-color: #E0E7FF;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: white;
            border-bottom: 3px solid #0A1128;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Chat Container */
        .chat-container {
            display: flex;
            flex: 1;
            height: calc(100vh - 110px);
            overflow: hidden;
        }

        .chat-list {
            background-color: #A1C2F1;
            width: 15%;
            padding: 10px;
            border-right: 1px solid #0A1128;
            overflow-y: auto;
        }

        .chat-list-item {
            background-color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .chat-list-item.active {
            background-color: #E0E7FF;
        }

        .chat-list-item:hover {
            background-color: #E0E7FF;
        }

        .chat-box-container {
            background-color: white;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-box {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .message.sent {
            align-self: flex-end;
            background-color: #A1C2F1;
        }

        .message.received {
            align-self: flex-start;
            background-color: #E0E7FF;
        }

        .message-box {
            display: flex;
            gap: 10px;
            padding: 10px;
            border-top: 1px solid #ccc;
            background-color: #f4f4f4;
        }

        .message-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .message-box button {
            padding: 10px;
            border: none;
            background-color: #0A1128;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .message-box button:hover {
            background-color: #1E3A8A;
        }

        /* Feedback Section */
        .feedback-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            max-width: 400px;
            margin: 0 auto;
            text-align: left;
        }

        .feedback-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .feedback-title {
            font-size: 16px;
            color: #0A1128;
        }

        .feedback-meta {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
        }

        .feedback-rating {
            font-size: 18px;
            color: #FFC107;
            margin-bottom: 10px;
        }

        .feedback-message {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
       
        /* Announcement Section */
        .announcement-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
            align-items: flex-start;
        }
        .announcement-card {
            position: relative;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px; /* Adjust the size of the card */
            padding: 15px;
            text-align: left;
        }
        .delete {
            position: absolute; /* Position the button inside the card */
            bottom: 10px; /* Place it at the bottom of the card */
            right: 10px; /* Align it to the right */
            background-color: #F87171;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .delete:hover {
            background-color: #EF4444;
        }

        .announcement-card img {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .announcement-card h3 {
            font-size: 16px;
            color: #0A1128;
            margin-bottom: 10px;
        }

        .announcement-card p {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        #announcements .tab-content {
        display: block; /* Make sure it's visible */
        padding: 20px;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        }

        /* Add Announcement Button */
        .addAnnouncement {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #0A1128;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .addAnnouncement:hover {
            background-color: #1E3A8A;
        }
        
        /* Announcement Modal styles */
        .announcement-modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .announcement-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form styles */
        #announcementForm {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /*trivia*/
        /* Trivia Section Styles */
        .trivia-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            justify-content: center;
            align-items: flex-start;
        }

        .trivia-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            padding: 15px;
            text-align: left;
            position: relative;
        }

        .trivia-card h3 {
            font-size: 16px;
            color: #0A1128;
            margin-bottom: 10px;
        }

        .trivia-card p {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }

        .addTrivia {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #0A1128;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .addTrivia:hover {
            background-color: #1E3A8A;
        }

        /* trivia modal */
        /* Modal Styles */
    .trivia-modal {
        display: none; /* Initially hidden */
        position: fixed;
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        overflow: auto; /* Enable scroll if needed */
        padding-top: 60px; /* Top padding */
    }

    /* Modal Content */
    .trivia-modal-content {
        background-color: white;
        margin: 5% auto; /* Center the modal */
        padding: 20px;
        border-radius: 10px;
        width: 40%; /* Adjust width as needed */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Close Button */
    .trivia-modal .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 25px;
        transition: 0.3s;
        cursor: pointer;
    }

    /* Close Button Hover Effect */
    .trivia-modal .close:hover,
    .trivia-modal .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Form Elements */
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    label {
        font-size: 16px;
        font-weight: bold;
    }
    /* Common input and textarea styles for both forms */
    input, textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
    }


    textarea {
        resize: vertical; /* Allows resizing the textarea vertically */
        min-height: 100px; /* Initial height */
    }

    /* Common Submit Button Styles */
    button[type="submit"] {
        padding: 10px;
        background-color: #0A1128;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    /* Submit Button Hover Effect */
    button[type="submit"]:hover {
        background-color: #1E3A8A;
    }

    .user-name {
    flex-grow: 1; /* Makes the name take available space */
}

.unread-count {
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 5px 10px;
    margin-left: 10px; /* Adds space between name and badge */
    font-size: 12px;
    display: none; /* Ensure it's hidden by default */
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
        <div class="dashboard-title">Content Manager</div>
        <div class="nav-icons">
            <a href="user_management.php" title="User Management">
                <img src="../MDO/twopeople.png" alt="User Management">
            </a>
            <a href="appointment_management.php" title="Appointment Management">
                <img src="../MDO/user_journal.png" alt="Appointment Management">
            </a>
            <a href="content_manager.php" title="Content Manager">
                <img src="../MDO/edit_yellow.png" alt="Content Manager">
            </a>
            <a href="admin_profile.php" title="Admin Profile">
                <img src="../MDO/profile.png" alt="Admin Profile">
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" data-tab="chat">Chat</div>
        <div class="tab" data-tab="feedback">Feedback</div>
        <div class="tab" data-tab="announcements">Announcements</div>
        <div class="tab" data-tab="trivia">Trivia</div>
    </div>

    <!-- Tab Content -->
    <div id="chat" class="tab-content active">
        <div class="chat-container">
            <div class="chat-list">
                <div class="chat-list-item active" data-chat="sasa">Sasa Jean</div>
                <div class="chat-list-item" data-chat="luke">Luke Alpine</div>
            </div>
            <div class="chat-box-container">
                <div class="chat-box" id="chatBox">
                    <!-- Default chat content for Sasa Jean -->
                    <div class="message received">Hello! How are you?</div>
                    <div class="message sent">I’m doing fine. How about you?</div>
                </div>
                <div class="message-box">
                    <input type="text" placeholder="Type your message..." id="messageInput">
                    <button id="sendMessage">Send</button>
                </div>
            </div>
        </div>
    </div>

    <div id="feedback" class="tab-content">
    <div style="padding: 20px; text-align: center; height: 100%;">
        <div class="feedback-card">
            <div class="feedback-header">
                <div class="feedback-title">
                    <strong>Sara Jean</strong>
                </div>
                <div class="feedback-meta">
                    General consultation<br>Oct 16, 2:00 PM
                </div>
            </div>
            <div class="feedback-rating">
                ★★★★☆
            </div>
            <div class="feedback-message">
                Good service! The staff were nice.
            </div>
        </div>
    </div>
    </div>

    <div id="feedback" class="tab-content">
        <div style="padding: 20px; text-align: center; height: 100%;">
            <div class="feedback-card">
                <div class="feedback-header">
                    <div class="feedback-title">
                        <strong>Sara Jean</strong>
                    </div>
                    <div class="feedback-meta">
                        General consultation<br>Oct 16, 2:00 PM
                    </div>
                </div>
                <div class="feedback-rating">
                    ★★★★☆
                </div>
                <div class="feedback-message">
                    Good service! The staff were nice.
                </div>
            </div>
        </div>
    </div>

    <div id="announcements" class="tab-content">
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; padding: 20px;">
            <div class="announcement-container">
            <!-- Existing Announcement Card 1 -->
            <div class="announcement-card" data-id="1">
                <img src="../MDO/sampleimage.jpg" alt="Announcement Image">
                <h3>New Announcement</h3>
                <p>This is a sample announcement text. Replace this content with actual announcement details.</p>
                <div class="delete">Delete</div>
            </div>
            </div>
        </div>

        <!-- Add Button -->
        <div>
            <div class="addAnnouncement">+</div>
        </div>
    </div>
    <!-- Modal for Adding New Announcement -->
    <div id="announcementModal" class="announcement-modal">
        <div class="announcement-modal-content">
            <span class="close">&times;</span>
            <h2>Add New Announcement</h2>
            <form id="announcementForm">
                <label for="announcementTitle">Title:</label>
                <input type="text" id="announcementTitle" name="announcementTitle" required>

                <label for="announcementText">Details:</label>
                <textarea id="announcementText" name="announcementText" required></textarea>

                <label for="announcementImage">Image URL:</label>
                <input type="url" id="announcementImage" name="announcementImage">

                <button type="submit">Add Announcement</button>
            </form>
        </div>
    </div>

        <!-- Trivia Section -->
        <div id="trivia" class="tab-content">
            <div class="trivia-container">
                <!-- Trivia Card 1 -->
                <div class="trivia-card">
                    <h3>PREVENTION IS KEY:</h3>
                    <p>Most oral health issues, like tooth decay and gum disease, can be prevented with good hygiene and regular check-ups.</p>
                    <div class="delete">Delete</div>
                </div>
            </div>

            <!-- Add Trivia Button -->
            <div class="addTrivia">+</div>
        </div>

        <!-- Add Trivia Modal -->
        <div id="triviaModal" class="trivia-modal">
            <div class="trivia-modal-content">
                <span class="close">&times;</span>
                <h2>Add New Trivia</h2>
                <form id="triviaForm">
                    <label for="triviaTitle">Title:</label>
                    <input type="text" id="triviaTitle" name="triviaTitle" required>

                    <label for="triviaText">Details:</label>
                    <textarea id="triviaText" name="triviaText" required></textarea>

                    <button type="submit">Add Trivia</button>
                </form>
            </div>
        </div>

    <script>
        // Tab switching logic
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and hide all contents
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                // Add active class to the clicked tab and show its content
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        loadUserList();

// Send message logic
document.getElementById("sendMessage").addEventListener("click", function () {
    const messageInput = document.getElementById("messageInput");
    const messageText = messageInput.value.trim();
    if (messageText && document.querySelector(".chat-list-item.active")) {
        const userEmail = document.querySelector(".chat-list-item.active").getAttribute("data-chat");
        sendMessageToUser(userEmail, messageText);
    } else {
        alert("Please select a user and enter a message.");
    }
});

// Listen for Enter key to send messages
document.getElementById("messageInput").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        document.getElementById("sendMessage").click();
    }
});

// Get unread message count
function getUnreadMessages(userEmail) {
    return fetch(`https://umakmdo-91b845374d5b.herokuapp.com/Admin/getUnreadMessages.php?email=${encodeURIComponent(userEmail)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Failed to load unread message count.");
            }
            return response.json();
        })
        .then(data => {
            if (data.unread_count !== undefined) {
                return data.unread_count;
            } else {
                throw new Error("Unread count not available.");
            }
        })
        .catch(error => {
            console.error("Error fetching unread messages:", error);
            return 0; // Return 0 if there's an error
        });
}

async function loadUserList() {
    try {
        const response = await fetch("https://umakmdo-91b845374d5b.herokuapp.com/Admin/get_users.php");
        if (!response.ok) {
            throw new Error("Failed to load users. Status: " + response.status);
        }
        const data = await response.json();
        
        const chatListDiv = document.getElementById("chatList");
        chatListDiv.innerHTML = "";
        if (data.length === 0) {
            chatListDiv.innerHTML = "<div>No users available.</div>";
        }

        for (const user of data) {
            const userElement = document.createElement("div");
            userElement.classList.add("chat-list-item");
            userElement.setAttribute("data-chat", user.umak_email);

            const userNameContainer = document.createElement("div");
            userNameContainer.classList.add("user-name-container");

            const userName = document.createElement("span");
            userName.classList.add("user-name");
            userName.textContent = user.first_name + ' ' + user.last_name;

            const unreadCountDiv = document.createElement("span");
            unreadCountDiv.classList.add("unread-count");
            unreadCountDiv.textContent = "0"; // Default unread count
            unreadCountDiv.style.display = "none"; // Hide by default

            userNameContainer.appendChild(userName);
            userNameContainer.appendChild(unreadCountDiv);

            userElement.appendChild(userNameContainer);
            chatListDiv.appendChild(userElement);

            userElement.addEventListener("click", () => {
                document.querySelectorAll(".chat-list-item").forEach(u => u.classList.remove("active"));
                userElement.classList.add("active");
                loadMessages(user.umak_email, userElement);
                markMessagesAsRead(user.umak_email, userElement);
            });

            const unreadMessages = await getUnreadMessages(user.umak_email);
            if (unreadMessages > 0) {
                unreadCountDiv.textContent = unreadMessages;
                unreadCountDiv.style.display = "inline-block"; // Show badge
            }
        }
    } catch (error) {
        console.error("Error loading user list:", error);
        alert("Error loading user list: " + error.message);
    }
}

function loadMessages(userEmail, userElement) {
    console.log("Loading messages for user:", userEmail);

    fetch("https://umakmdo-91b845374d5b.herokuapp.com/Admin/get_messages.php?user_email=" + encodeURIComponent(userEmail))
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to load messages. Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const chatBox = document.getElementById("chatBox");

            chatBox.innerHTML = ""; // Clear the chat box

            let unreadCount = 0;

            if (!Array.isArray(data) || data.length === 0) {
                const noMessageDiv = document.createElement("div");
                noMessageDiv.classList.add("no-messages");
                noMessageDiv.textContent = "No messages yet.";
                chatBox.appendChild(noMessageDiv);
            } else {
                data.forEach(message => {
                    const messageDiv = document.createElement("div");

                    const messageContentDiv = document.createElement("div");
                    messageContentDiv.classList.add("message-content");
                    messageContentDiv.textContent = message.message;

                    if (message.receiver_email === userEmail && message.status === "unread") {
                        unreadCount++;
                    }

                    if (message.sender_email === userEmail) {
                        messageDiv.classList.add("message", "received");
                    } else if (message.receiver_email === userEmail) {
                        messageDiv.classList.add("message", "sent");
                    }

                    messageDiv.appendChild(messageContentDiv);
                    chatBox.appendChild(messageDiv);
                });
            }

            const unreadCountDiv = userElement.querySelector(".unread-count");

            if (unreadCount === 0) {
                unreadCountDiv.style.display = "none";  // Hide the badge
            } else {
                unreadCountDiv.style.display = "inline-block";
                unreadCountDiv.textContent = unreadCount; // Update unread count
            }
        })
        .catch(error => {
            console.error("Error loading messages:", error);
        });
}

function sendMessageToUser(userEmail, messageText) {
    fetch("https://umakmdo-91b845374d5b.herokuapp.com/Admin/send_message.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            sender_email: "admin2@example.com", // Replace with dynamic sender email
            receiver_email: userEmail,
            message: messageText
        })
    })
    .then(response => {
        if (response.ok) {
            console.log("Message sent successfully");
            loadMessages(userEmail);
        } else {
            throw new Error("Failed to send message.");
        }
    })
    .catch(error => {
        console.error("Error sending message:", error);
    });
}

function markMessagesAsRead(userEmail, userElement) {
    fetch("https://umakmdo-91b845374d5b.herokuapp.com/Admin/mark_messages_read.php?user_email=" + encodeURIComponent(userEmail))
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to mark messages as read. Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateChatUIToShowMessagesAsRead(userElement);
            }
        })
        .catch(error => {
            console.error("Error marking messages as read:", error);
        });
}

function updateChatUIToShowMessagesAsRead(userElement) {
    const unreadCountDiv = userElement.querySelector(".unread-count");
    unreadCountDiv.style.display = "none";  // Hide unread count
}

    //announcement script
    document.addEventListener('DOMContentLoaded', async function () {
    const announcementContainer = document.querySelector('#announcements .announcement-container');
    // Get the button that opens the modal
    const modal = document.getElementById('announcementModal');
    const addButton = document.querySelector('.addAnnouncement');
    // Get the <span> element that closes the modal
    const closeButton = document.querySelector('#announcementModal .close');

   // When the user clicks on the button, open the modal
    addButton.addEventListener('click', function () {
        modal.style.display = 'block';
    });

    // Close modal when close button is clicked
    closeButton.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Fetch announcements from the database on page load
    try {
        const response = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/Admin/announcements.php', { method: 'GET' });
        const announcements = await response.json();

       // Clear the announcements container (optional, in case of duplicates)
        announcementContainer.innerHTML = '';

         // Populate announcement container with data from the database
        announcements.forEach(announcement => {
            const card = document.createElement('div');
            card.classList.add('announcement-card');
            
            card.dataset.id = announcement.id;

            const img = document.createElement('img');
            img.src = announcement.image_url || '../MDO/sampleimage.jpg'; // Default image if none provided
            img.alt = "Announcement Image";

            const title = document.createElement('h3');
            title.textContent = announcement.title;

            const details = document.createElement('p');
            details.textContent = announcement.details;

            const deleteButton = document.createElement('div');
            deleteButton.classList.add('delete');
            deleteButton.textContent = 'Delete';

            deleteButton.addEventListener('click', async function () {
            const id = card.dataset.id;  // Make sure the `id` is correctly set on the element

            if (confirm('Are you sure you want to delete this announcement?')) {
                try {
                    // Send DELETE request with ID in the body as JSON (instead of URLSearchParams)
                    const deleteResponse = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/Admin/announcements.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',  // Set content type to JSON
                        },
                        body: JSON.stringify({ id: id }),  // Send the id in JSON format
                    });

                    const result = await deleteResponse.json();

                    if (result.status === 'success') {
                        alert('Announcement deleted successfully!');
                        card.remove(); // Remove the card from the DOM
                    } else {
                        alert('Failed to delete announcement: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error deleting announcement:', error);
                }
            }
        });

            card.appendChild(img);
            card.appendChild(title);
            card.appendChild(details);
            card.appendChild(deleteButton);

            announcementContainer.appendChild(card);
        });
    } catch (error) {
        console.error('Error fetching announcements:', error);
    }
});
    // Handle form submission to add a new announcement (POST request)
   document.getElementById('announcementForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const title = document.getElementById('announcementTitle').value;
    const details = document.getElementById('announcementText').value;
    const imageUrl = document.getElementById('announcementImage').value;

    if (title && details) {
        try {
            const response = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/Admin/announcements.php', {
                method: 'POST',
                body: new URLSearchParams({ title, details, image_url: imageUrl }),
            });

            const result = await response.json();
            if (result.status === 'success') {
                alert('Announcement added successfully!');
               
                // Dynamically add the new announcement to the page
                const newCard = document.createElement('div');
                newCard.classList.add('announcement-card');

                newCard.dataset.id = result.id;

                const newImage = document.createElement('img');
                newImage.src = imageUrl || '../MDO/sampleimage.jpg';
                newImage.alt = "Announcement Image";

                const newTitle = document.createElement('h3');
                newTitle.textContent = title;

                const newText = document.createElement('p');
                newText.textContent = details;

                const deleteButton = document.createElement('div');
                deleteButton.classList.add('delete');
                deleteButton.textContent = 'Delete';

                deleteButton.addEventListener('click', async function () {
                const id = newCard.dataset.id;  // Make sure the `id` is correctly set on the element

                if (confirm('Are you sure you want to delete this announcement?')) {
                    try {
                        // Send DELETE request with ID in the body as JSON (instead of URLSearchParams)
                        const deleteResponse = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/Admin/announcements.php', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',  // Set content type to JSON
                            },
                            body: JSON.stringify({ id: id }),  // Send the id in JSON format
                        });

                        const result = await deleteResponse.json();

                        if (result.status === 'success') {
                            alert('Announcement deleted successfully!');
                            newCard.remove(); // Remove the card from the DOM
                        } else {
                            alert('Failed to delete announcement: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error deleting announcement:', error);
                    }
                }
                });

                newCard.appendChild(newImage);
                newCard.appendChild(newTitle);
                newCard.appendChild(newText);
                newCard.appendChild(deleteButton);

                // Append the new card to the container
                const container = document.querySelector('#announcements .announcement-container');
                container.appendChild(newCard);

                // Close the modal and reset the form
                
                document.getElementById('announcementForm').reset();
                const modal = document.getElementById('announcementModal');
                if (modal) modal.style.display = 'none';
            } else {
                alert('Failed to add announcement: ' + result.message);
            }
        } catch (error) {
            console.error('Error adding announcement:', error);
        }
    } else {
        alert('Please fill in all required fields.');
    }
});
//trivia script
    document.addEventListener('DOMContentLoaded', async function () {
        const triviaContainer = document.querySelector('#trivia .trivia-container');
        // Get the button that opens the modal
        const modal = document.getElementById('triviaModal');
        const addButton = document.querySelector('.addTrivia');

        // Get the <span> element that closes the modal
        const closeButton = document.querySelector('#triviaModal .close');

        // When the user clicks on the button, open the modal
        addButton.addEventListener('click', function () {
            modal.style.display = 'block';
        });

        // When the user clicks on <span> (x), close the modal
        closeButton.addEventListener('click', function () {
        modal.style.display = 'none';
    });
    // Fetch trivia from the database on page load
    try {
        const response = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/trivia.php', { method: 'GET' });
        const triviaList = await response.json();

        // Clear the trivia container (optional, in case of duplicates)
        triviaContainer.innerHTML = '';

        // Populate trivia container with data from the database
        triviaList.forEach(trivia => {
            const newCard = document.createElement('div');
            newCard.classList.add('trivia-card');

            const newTitle = document.createElement('h3');
            newTitle.textContent = trivia.title;

            newCard.dataset.id = trivia.id;

            const newText = document.createElement('p');
            newText.textContent = trivia.details;

            const deleteButton = document.createElement('div');
            deleteButton.classList.add('delete');
            deleteButton.textContent = 'Delete';

            deleteButton.addEventListener('click', async function () {
            const id = newCard.dataset.id;  // Make sure the `id` is correctly set on the element

            if (confirm('Are you sure you want to delete this trivia?')) {
                try {
                    // Send DELETE request with ID in the body as JSON (instead of URLSearchParams)
                    const deleteResponse = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/trivia.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',  // Set content type to JSON
                        },
                        body: JSON.stringify({ id: id }),  // Send the id in JSON format
                    });

                    const result = await deleteResponse.json();

                    if (result.status === 'success') {
                        alert('Trivia deleted successfully!');
                        newCard.remove(); // Remove the card from the DOM
                    } else {
                        alert('Failed to delete Trivia: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error deleting Trivia:', error);
                }
            }
        });

            newCard.appendChild(newTitle);
            newCard.appendChild(newText);
            newCard.appendChild(deleteButton);

            triviaContainer.appendChild(newCard);
        });
    } catch (error) {
        console.error('Error fetching trivia:', error);
    }
    });

    // Handle form submission to add new trivia
    document.getElementById('triviaForm').addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent form from submitting normally

        const title = document.getElementById('triviaTitle').value;
        const text = document.getElementById('triviaText').value;

        if (title && text) {
            try {
                console.log('Submitting trivia:', { title, text });
                const response = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/trivia.php', {
                    method: 'POST',
                    body: new URLSearchParams({ title: title, details: text }),
                });

                const result = await response.json();
                console.log('Fetch result:', result);

                if (result.status === 'success') {
                    alert('Trivia added successfully!');

                    // Dynamically add the trivia to the container without refreshing
                    const newCard = document.createElement('div');
                    newCard.classList.add('trivia-card');

                    newCard.dataset.id = result.id;

                    const newTitle = document.createElement('h3');
                    newTitle.textContent = title;

                    const newText = document.createElement('p');
                    newText.textContent = text;

                    const deleteButton = document.createElement('div');
                    deleteButton.classList.add('delete');
                    deleteButton.textContent = 'Delete';
                    
                    deleteButton.addEventListener('click', async function () {
                const id = newCard.dataset.id;  // Make sure the `id` is correctly set on the element

                if (confirm('Are you sure you want to delete this trivia?')) {
                    try {
                        // Send DELETE request with ID in the body as JSON (instead of URLSearchParams)
                        const deleteResponse = await fetch('https://umakmdo-91b845374d5b.herokuapp.com/trivia.php', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',  // Set content type to JSON
                            },
                            body: JSON.stringify({ id: id }),  // Send the id in JSON format
                        });

                        const result = await deleteResponse.json();

                        if (result.status === 'success') {
                            alert('Trivia deleted successfully!');
                            newCard.remove(); // Remove the card from the DOM
                        } else {
                            alert('Failed to delete Trivia: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error deleting Trivia:', error);
                    }
                }
                });

                    newCard.appendChild(newTitle);
                    newCard.appendChild(newText);
                    newCard.appendChild(deleteButton);

                    const container = document.querySelector('#trivia .trivia-container');
                    container.appendChild(newCard);
                    
                    // Clear the form
                    document.getElementById('triviaForm').reset();

                    // Close the modal
                    const modal = document.getElementById('triviaModal');
                    if (modal) modal.style.display = 'none';
                } else {
                    alert('Failed to add trivia: ' + result.message);
                }
            } catch (error) {
                console.error('Error during fetch:', error);
                alert('An error occurred. Please try again.');
            }
        } else {
            alert("Please fill in both title and details.");
        }
    });
    </script>
</body>
</html>