<?php
// Include the database connection file
include('connection.php');

// Fetch products sorted by id (most recently added), including category and description information
$sql = "SELECT products.*, categories.name AS category FROM products 
        LEFT JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id ASC"; // Sorting by 'id' in ascending order
$result = $mysqli->query($sql);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="style/home.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .dashboard-container {
            display: flex;
            height: calc(100vh - 70px);
            margin-top: 70px;
        }

        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            padding: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            text-decoration: none;
            font-size: 16px;
            color: #666;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #e74c3c;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .customers {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .customers h2 {
            margin: 0;
            font-size: 24px;
        }

        .add-customer-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .add-customer-btn:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f9f9f9;
        }

        .action-btn {
            display: flex;
            gap: 10px;
            /* This creates a gap between the buttons */
            justify-content: center;
            /* Centers the buttons */
        }

        .action-btn .edit-btn,
        .action-btn .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .edit-btn {
            background-color: #2ecc71;
            color: #fff;
        }

        .edit-btn:hover {
            background-color: #27ae60;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: #fff;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo"><img src="./image/Matra.png" alt="Logo"></a>
    </header>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="./admin_dashboard.php">Customer</a></li>
                <li><a href="#" class="active">Product</a></li>
                <li><a href="./manage_orders.php">Manage Orders</a></li> <!-- Added link for Manage Orders -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Product List</h1>
            <div class="customers">
                <div class="products-header">
                    <h2>All Products</h2>
                    <a href="add_new_product.php" class="add-customer-btn">Add New Product</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th> <!-- Added column for Description -->
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td> <!-- Display the Description -->
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['stock']) ?></td>
                                    <div class="action_btn">
                                        <td>
                                            <a href="editProduct.php?id=<?= urlencode($row['id']) ?>" class="action-btn edit-btn">Edit</a>
                                            <a href="delete-product.php?id=<?= urlencode($row['id']) ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                        </td>
                                    </div>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>