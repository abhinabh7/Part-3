<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $logged_in = false;
} else {
    $logged_in = true;
    // Check if session variables are set before accessing them
    $user_fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Guest'; // Default to 'Guest' if not set
    $user_contact = isset($_SESSION['contact']) ? $_SESSION['contact'] : 'Not available'; // Default if not set
    $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Not available'; // Default if not set
    $user_country = isset($_SESSION['country']) ? $_SESSION['country'] : 'Not available'; // Default if not set
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .edit-btn {
            background-color: #ff4d4d;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .edit-btn:hover {
            background-color: #e04343;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo"><img src="./image/Matra.png" alt=""></a>
    </header>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="#" class="active">Profile</a></li>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Shop</a></li>
                <li><a href="customer_order_status.php">Order</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if ($logged_in): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main">
            <div class="header">
                <h2>Welcome
                    <?php if ($logged_in): ?>
                        <span id="welcome-name"><?php echo htmlspecialchars($user_fullname); ?></span>
                    <?php else: ?>
                        <span>Please sign in to view your profile</span>
                    <?php endif; ?>
                </h2>
            </div>

            <?php if ($logged_in): ?>
                <div class="profile-section">
                    <h3 id="profile-name"><?php echo htmlspecialchars($user_fullname); ?></h3>
                    <p><span>Contact: </span><span id="contact"><?php echo htmlspecialchars($user_contact); ?></span></p>
                    <p><span>Email: </span><span id="email"><?php echo htmlspecialchars($user_email); ?></span></p>
                    <p><span>Country: </span><span id="country"><?php echo htmlspecialchars($user_country); ?></span></p>
                    <button class="edit-btn" onclick="toggleEditMode()">Edit</button>
                </div>
            <?php else: ?>
                <div class="no-login-message">
                    <p>Please <a href="login.php">sign in</a> to access your profile.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let editMode = false;

        function toggleEditMode() {
            const profileSection = document.querySelector('.profile-section');
            const editButton = document.querySelector('.edit-btn');
            const details = ['profile-name', 'contact', 'email', 'country'];

            if (!editMode) {
                // Convert text to input fields for editing
                details.forEach(id => {
                    const element = document.getElementById(id);
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = element.textContent;
                    input.id = id;
                    element.replaceWith(input);
                });
                editButton.textContent = 'Save';
            } else {
                // Convert input fields back to text
                details.forEach(id => {
                    const input = document.getElementById(id);
                    const span = document.createElement('span');
                    span.id = id;
                    span.textContent = input.value;
                    input.replaceWith(span);
                });

                // Update the Welcome message with the new name
                const welcomeName = document.getElementById('welcome-name');
                const updatedName = document.getElementById('profile-name').textContent;
                welcomeName.textContent = updatedName;

                editButton.textContent = 'Edit';
            }

            editMode = !editMode;
        }

        
    </script>
</body>

</html>
