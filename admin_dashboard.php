<?php
require_once 'connection.php';  // Include the connection file
session_start();

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    // If not an admin, redirect to an access denied page or show an error message
    header('Location: access_denied.php');
    exit;
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
</head>

<body>

    <header>
        <a href="index.php" class="logo"><img src="./image/Matra.png" alt="Logo"></a>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="./admin_d.php" class="active">Customer</a></li>
                <li><a href="./product_list.php">Product</a></li>
                <li><a href="./manage_orders.php">Manage Orders</a></li> <!-- Added link for Manage Orders -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <h1>Admin,</h1>
            <div class="summary-cards">
                <!-- Total Customers Card -->
                <div class="card">
                    <?php
                    // Fetch total customers
                    $sqlTotalCustomers = "SELECT COUNT(*) AS total_customers FROM clients";
                    $resultTotalCustomers = $mysqli->query($sqlTotalCustomers);  // Use $mysqli here

                    if ($resultTotalCustomers && $rowTotalCustomers = $resultTotalCustomers->fetch_assoc()) {
                        $totalCustomers = $rowTotalCustomers['total_customers'];
                    } else {
                        $totalCustomers = 0; // Fallback if query fails
                    }
                    ?>
                    <h2><?php echo $totalCustomers; ?></h2>
                    <p>Total Customers</p>
                </div>

            </div>

            <!-- Customers Table -->
            <div class="customers">
                <h2>
                    All Customers
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) { ?>
                        <a href="add_customer.php" class="add-customer-btn">Add Customer</a>
                    <?php } ?>
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all customers
                        $sql = "SELECT * FROM clients";
                        $result = $mysqli->query($sql);  // Use $mysqli here

                        if (!$result) {
                            die("Invalid query: " . $mysqli->error);
                        }

                        while ($row = $result->fetch_assoc()) {
                            echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                        <td>
                            ";
                            // Display actions only if the user is an admin
                            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                                echo "<a href='edit_customer.php?id={$row['id']}' class='action-btn edit-btn'>Edit</a>
                                      <a href='delete_customer.php?id={$row['id']}' class='action-btn delete-btn'>Delete</a>";
                            }
                            echo "</td>
                    </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>