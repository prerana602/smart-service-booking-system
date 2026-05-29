<?php
session_start();
require_once 'db_connect.php';

// Security check: Only allow admins to view this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied. You must be an administrator to view this page. <a href='login.php'>Login</a>");
}

// Handle updating booking status
if (isset($_GET['complete_id'])) {
    $id = $_GET['complete_id'];
   // SECURE FIX: Using prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" means integer
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin.php"); // Refresh page
    exit();
}

// Fetch all bookings
$result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UrbanAssist - Admin</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #cbd5e1; text-decoration: none; font-weight: bold; }
        .navbar a:hover { color: white; }
        .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
        h2 { margin-top: 0; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
        tr:hover { background: #f8fafc; }
        .status-pending { background: #fef08a; color: #854d0e; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-completed { background: #bbf7d0; color: #166534; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .action-btn { background: #2563eb; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; }
        .action-btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Recent Service Bookings</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Service Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['customer_phone']; ?></td>
                <td><?php echo $row['service_type']; ?></td>
                <td><?php echo $row['booking_date']; ?></td>
                <td>
                    <?php if($row['status'] == 'Pending'): ?>
                        <span class="status-pending">Pending</span>
                    <?php else: ?>
                        <span class="status-completed">Completed</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['status'] == 'Pending'): ?>
                        <a href="admin.php?complete_id=<?php echo $row['id']; ?>" class="action-btn">Mark Done</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>