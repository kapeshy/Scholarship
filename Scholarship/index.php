<?php
session_start();
include 'db.php'; // Assuming db.php contains your database connection

$signup_error = $login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];

    $stmt = $conn->prepare("INSERT INTO signup (fullname, email, password, phone, dob)
                            VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("sssss", $fullname, $email, $password, $phone, $dob);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Signup successful! Please login.";
        header("Location: home.html");
        exit();
    } else {
        $signup_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['user_name'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM signup WHERE email = ?");
    
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("s", $username);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] =$username;
            echo "Login successful!";
            header("Location: home.html");
            exit();
        
    
        } else {
            $login_error = "Invalid password";
        }
    } else {
        $login_error = "User not found";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application - Login / Signup</title>     
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
        }

        /* Header styles */
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            max-width: 50px;
        }

        nav.menu {
            text-align: right;
        }

        nav.menu a {
            color: deeppink;
            text-decoration: none;
            margin-left: 20px;
            padding: 10px;
        }

        nav.menu a.active {
            background-color: #333; /* Highlight the active menu item */
            border-radius: 4px;
        }

        nav.menu a:hover {
            background-color: blue;
            color: #333;
        }

        /* Container for the signup form */
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"] {
            width: calc(100% - 10px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        #login-section {
            display: none; /* Hide login section by default */
        }
    </style>
    <script>
        function showLogin() {
            document.getElementById('signup-section').style.display = 'none';
            document.getElementById('login-section').style.display = 'block';
        }

        function showSignup() {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('signup-section').style.display = 'block';
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo.jpg" alt="logo" style="height: 50px;">
            <h2>Scholarship Application</h2>
        </div>
        <nav class="menu">
            <a href="#" class="active">Home</a>
            <!-- Add more menu items as needed -->
        </nav>
    </header>

    <div class="container">
        <!-- Signup Section -->
        <div id="signup-section">
            <h2>Signup</h2>
            <?php if (!empty($signup_error)) : ?>
                <div class="error-message"><?php echo $signup_error; ?></div>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="fullname">Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob">
                </div>
                <div class="form-group">
                    <button type="submit" name="signup">Sign Up</button>
                    <p>Already have an account? <a href="#login-section" onclick="showLogin()">Login here</a></p>
                </div>
            </form>
        </div>

        <!-- Login Section -->
        <div id="login-section">
            <h2>Login</h2>
            <?php if (!empty($login_error)) : ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <input type="text" name="user_name" placeholder="Email" required><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <button type="submit" name="login">Login</button><br><br>
                <p>Don't have an account? <a href="#signup-section" onclick="showSignup()">Sign Up here</a></p>
            </form>
        </div>
    </div>
    <script>
        function showLogin() {
            document.getElementById('signup-section').style.display = 'none';
            document.getElementById('login-section').style.display = 'block';
        }

        function showSignup() {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('signup-section').style.display = 'block';
        }
    </script>
</body>
</html>