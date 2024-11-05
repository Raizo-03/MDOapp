<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        /* Body and container styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #0A1744; /* Dark blue background */
            font-family: Arial, sans-serif;
            color: #FFFFFF;
        }
        
        .login-container {
            display: flex;
            align-items: center;
            background-color: #0A1744;
            padding: 40px;
            border-radius: 8px;
        }

        /* Logo styling */
        .logo-container {
            margin-right: 40px;
            text-align: center;
        }

        .logo-container img {
            width: 120px;
            height: auto;
        }

        .logo-container h2 {
            margin: 10px 0;
            font-size: 16px;
        }

        /* Form styling */
        .form-container {
            max-width: 250px;
        }

        .form-container label,
        .form-container input {
            display: block;
            width: 100%;
            color: #FFFFFF;
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            background-color: #F0F0F0;
            color: #000000; 
            border: none;
            border-radius: 4px;
        }

        /* Button styling */
        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #FFDD00; /* Yellow button color */
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Link styling */
        .form-container a {
            display: block;
            text-align: center;
            color: #FFFFFF;
            margin-top: 10px;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo and text section -->
        <div class="logo-container">
            <img src="path/to/umak_logo.png" alt="University of Makati Logo">
            <h2>UNIVERSITY OF MAKATI<br>Medical and Dental Office</h2>
        </div>

        <!-- Login form section -->
        <div class="form-container">
            <form action="admin_login_action.php" method="post">
                <label for="username">Enter username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Enter password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">LOG IN</button>
            </form>
            <a href="#">Forgot Password</a>
        </div>
    </div>
</body>
</html>
