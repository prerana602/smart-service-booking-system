<?php
// index.php
session_start();

// SECURITY CHECK: If the user is not logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$message = "";

// FETCH SERVICES FOR THE UI
$services_query = $conn->query("SELECT * FROM services ORDER BY id ASC");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['customer_name'];
    $phone = $_POST['customer_phone'];
    $service = $_POST['service_type']; // This will now catch the service name AND price
    $date = $_POST['booking_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (customer_name, customer_phone, service_type, booking_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $service, $date);

    if ($stmt->execute()) {
        $message = "<div class='alert success'>Booking successful! Our professional will contact you shortly.</div>";
    } else {
        $message = "<div class='alert error'>Error processing booking. Please try again.</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanAssist - Book a Service</title>
    <style>
        /* --- NEW HERO SECTION CSS --- */
        .hero-section {
            text-align: center;
            padding: 40px 20px 10px;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-section h2 {
            font-size: 36px;
            color: var(--text-main);
            margin-bottom: 15px;
            font-weight: 800;
        }

        .hero-section h2 span {
            color: var(--primary); /* Makes part of the title blue */
        }

        .hero-section p.subtitle {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .badge {
            background: #e0e7ff;
            color: #3730a3;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Logo styling for the navbar */
        /* Updated Logo styling for the navbar */
        .brand-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .brand-logo {
            height: 35px; /* Keeps the logo the perfect size for the navbar */
            width: auto;
            border-radius: 4px; /* Optional: rounds the corners slightly */
        }

        /* Modern CSS Design */
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body { background-color: var(--background); color: var(--text-main); min-height: 100vh; }

        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar .brand { font-size: 18px; font-weight: bold; color: white; text-decoration: none; }
        .navbar .nav-links { display: flex; gap: 20px; align-items: center; font-size: 14px; }
        .navbar a { color: #cbd5e1; text-decoration: none; font-weight: 600; transition: color 0.3s; }
        .navbar a:hover { color: white; } 
        .welcome-text { color: #94a3b8; margin-right: 10px; }

        .main-content { display: flex; justify-content: center; padding: 40px 20px; }

        /* Increased max-width to accommodate the grid */
        .booking-container { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); width: 100%; max-width: 650px; padding: 40px; }
        
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); font-size: 24px; margin-bottom: 8px; }
        .header p { color: var(--text-muted); font-size: 14px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label.title-label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        .form-group input[type="text"], .form-group input[type="tel"], .form-group input[type="date"] { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 15px; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }

        /* --- NEW VISUAL SERVICE GRID CSS --- */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Hide the actual radio button circle */
        .service-grid input[type="radio"] {
            display: none;
        }

        /* Style the card */
        .service-card {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .service-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .service-card h4 {
            font-size: 16px;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .service-card .price {
            color: var(--primary);
            font-weight: bold;
            font-size: 18px;
        }

        /* When the hidden radio button is checked, apply these styles to the card */
        .service-grid input[type="radio"]:checked + .service-card {
            border-color: var(--primary);
            background-color: #eff6ff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transform: translateY(-2px);
        }

        /* Add a small checkmark when selected */
        .service-grid input[type="radio"]:checked + .service-card::after {
            content: "✓";
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        /* ----------------------------------- */

        .submit-btn { width: 100%; padding: 14px; background-color: var(--primary); color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.3s; margin-top: 10px; }
        .submit-btn:hover { background-color: var(--primary-hover); }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .alert.success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Responsive design for mobile */
        @media (max-width: 500px) {
            .service-grid { grid-template-columns: 1fr; }
        }

        /* --- NEW FOOTER CSS --- */
        .footer {
            background-color: var(--text-main); /* Matches the dark navbar */
            color: #cbd5e1;
            padding: 50px 20px 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            color: white;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-section p {
            line-height: 1.6;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #334155;
            font-size: 13px;
        }

    </style>
</head>
<body>

   <?php include 'navbar.php'; ?>
        
    <div class="hero-section">
        <h2>Your Home, <span>Our Responsibility.</span></h2>
        <p class="subtitle">Book trusted, background-verified professionals for all your home service needs in just a few clicks.</p>
        
        <div class="trust-badges">
            <div class="badge">⭐ 4.8/5 Average Rating</div>
            <div class="badge">✅ Verified Experts</div>
            <div class="badge">🛡️ 100% Quality Guarantee</div>
        </div>
    </div>

    <div class="main-content">
        <div class="booking-container">
            <div class="header">
                <h1>Book a Service</h1>
                <p>Select a service below to see exact pricing.</p>
            </div>

            <?php echo $message; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label class="title-label" for="customer_name">Full Name</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="title-label" for="customer_phone">Phone Number (+91)</label>
                    <input type="tel" id="customer_phone" name="customer_phone" required 
                           pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits" placeholder="XXXXX XXXXX">
                </div>

                <div class="form-group">
                    <label class="title-label">Choose a Service</label>
                    
                <div class="service-grid">
                        <?php while($service = $services_query->fetch_assoc()): ?>
                        <label>
                            <input type="radio" name="service_type" value="<?php echo htmlspecialchars($service['service_name'] . ' - ₹' . $service['price']); ?>" required>
                            <div class="service-card">
                                <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                                <h4><?php echo htmlspecialchars($service['service_name']); ?></h4>
                                <div class="price">₹<?php echo htmlspecialchars($service['price']); ?></div>
                            </div>
                        </label>
                        <?php endwhile; ?>
                    </div>

                <div class="form-group">
                    <label class="title-label" for="booking_date">Preferred Date</label>
                    <input type="date" id="booking_date" name="booking_date" required>
                </div>

                <button type="submit" class="submit-btn">Confirm Booking</button>
            </form>
        </div>
    </div>
 
    </div> 

    <div class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About UrbanAssist</h3>
                <p>UrbanAssist is a premium local service booking platform designed for modern households. We connect you with verified, skilled professionals for plumbing, electrical, and cleaning services right at your doorstep.</p>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>📍 123 Gaurav Garden, Raipur City</p>
                <p>📞 +91 8643047964</p>
                <p>✉️ support@urbanassist.com</p>
            </div>
            
            <div class="footer-section">
                <h3>Working Hours</h3>
                <p>Monday - Saturday: 8:00 AM - 8:00 PM</p>
                <p>Sunday: 9:00 AM - 5:00 PM</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> UrbanAssist. Final Year BCA Project.</p>
        </div>
    </div>

</body>
</html>