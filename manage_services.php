<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$message = "";

// Handle adding new service
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO services (service_name, description, price, image_url) VALUES (?, ?, ?, 'images/default.jpg')");
    $stmt->bind_param("ssd", $name, $desc, $price);
    if($stmt->execute()) $message = "<div style='color:green; padding:10px;'>Service Added!</div>";
    $stmt->close();
}

$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - Admin</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between;}
        .navbar a { color: white; text-decoration: none; margin-left:15px; font-weight: bold;}
        .container { padding: 30px; max-width: 1000px; margin: 0 auto; display: flex; gap: 20px;}
        .card { background: white; padding: 20px; border-radius: 8px; flex: 1; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing:border-box;}
        button { width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left;}
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card" style="flex: 0.5;">
            <h3>Add New Service</h3>
            <?php echo $message; ?>
            <form method="POST">
                <input type="hidden" name="add_service" value="1">
                <label>Service Name</label>
                <input type="text" name="service_name" required>
                <label>Description</label>
                <textarea name="description" rows="3" required></textarea>
                <label>Price (₹)</label>
                <input type="number" step="0.01" name="price" required>
                <button type="submit">Add Service</button>
            </form>
        </div>

        <div class="card">
            <h3>Current Services</h3>
            <table>
                <tr><th>ID</th><th>Name</th><th>Price</th></tr>
                <?php while($row = $services->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td>₹<?php echo $row['price']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>