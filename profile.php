<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch user's bookings (matching by name for now based on your schema)
$stmt = $conn->prepare("SELECT * FROM bookings WHERE customer_name = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - UrbanAssist</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }
        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #cbd5e1; text-decoration: none; font-weight: bold; margin-left: 15px;}
        .navbar a:hover { color: white; }
        .container { padding: 40px; max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px;}
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .pending { background: #fef08a; color: #854d0e; }
        .completed { background: #bbf7d0; color: #166534; }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p>Manage your profile and view your booking history below.</p>
        </div>

        <div class="card">
            <h3>My Booking History</h3>
            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($row['status']); ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if($result->num_rows == 0) echo "<tr><td colspan='4'>No bookings found.</td></tr>"; ?>
            </table>
        </div>
    </div>
</body>
</html>