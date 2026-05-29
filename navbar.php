<div class="navbar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <a href="index.php" style="color: white; text-decoration: none; font-size: 20px; font-weight: bold;">UrbanAssist</a>
    </div>
    
    <div style="display: flex; gap: 15px; align-items: center;">
        <?php if(isset($_SESSION['user_name'])): ?>
            <span style="color: #cbd5e1;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'): ?>
            <a href="index.php" style="color: white; text-decoration: none; font-weight: bold;">Book Service</a>
            <a href="profile.php" style="color: white; text-decoration: none; font-weight: bold;">My Profile</a>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin.php" style="color: #fef08a; text-decoration: none; font-weight: bold;">Admin Dashboard</a>
            <a href="manage_services.php" style="color: white; text-decoration: none; font-weight: bold;">Manage Services</a>
        <?php endif; ?>
        
        <a href="logout.php" style="color: white; text-decoration: none; font-weight: bold;">Logout</a>
    </div>
</div>