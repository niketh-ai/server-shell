<?php
// index.php - Main Admin Panel
require_once 'includes/LicenseManager.php';

$licenseManager = new LicenseManager();

// Handle actions
$alert = null;
if ($_POST['action'] === 'create_license') {
    $result = $licenseManager->createLicense(
        $_POST['user_key'],
        $_POST['hwid'],
        $_POST['days'] ?? 30
    );
    
    $alert = $result['success'] 
        ? ['type' => 'success', 'message' => $result['message'] . ' Expires: ' . $result['expiry_date']]
        : ['type' => 'error', 'message' => $result['message']];
}

if ($_GET['action'] === 'delete' && $_GET['key']) {
    $result = $licenseManager->deleteLicense($_GET['key']);
    $alert = $result['success'] 
        ? ['type' => 'success', 'message' => $result['message']]
        : ['type' => 'error', 'message' => $result['message']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reaper Mods • License Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-ghost"></i>
            </div>
            <h1 class="title">Reaper Mods Portal</h1>
            <p class="subtitle">File-Based License System • Nightmare Edition</p>
        </div>

        <!-- Stats -->
        <?php
        $stats = $licenseManager->getStats();
        echo '
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">' . $stats['total'] . '</div>
                <div class="stat-label">Total Licenses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: var(--success)">' . $stats['active'] . '</div>
                <div class="stat-label">Active Licenses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: var(--danger)">' . $stats['expired'] . '</div>
                <div class="stat-label">Expired Licenses</div>
            </div>
        </div>';
        ?>

        <!-- Alerts -->
        <?php if ($alert): ?>
            <div class="alert alert-<?= $alert['type'] === 'success' ? 'success' : 'error' ?>">
                <i class="fas fa-<?= $alert['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= $alert['message'] ?>
            </div>
        <?php endif; ?>

        <!-- Create License Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key card-icon"></i>
                <h3 class="card-title">Generate New License</h3>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="create_license">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">User Key</label>
                        <input type="text" name="user_key" class="form-input" placeholder="Enter unique user key" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hardware ID</label>
                        <input type="text" name="hwid" class="form-input" placeholder="Enter HWID" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Validity Period</label>
                        <select name="days" class="form-select">
                            <option value="1">24 Hours</option>
                            <option value="7">7 Days</option>
                            <option value="30" selected>30 Days</option>
                            <option value="90">90 Days</option>
                            <option value="365">1 Year</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-plus"></i> Create License
                    </button>
                </div>
            </form>
        </div>

        <!-- License Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list card-icon"></i>
                <h3 class="card-title">License Database</h3>
            </div>
            
            <div class="table-container">
                <table class="license-table">
                    <thead>
                        <tr>
                            <th>User Key</th>
                            <th>Hardware ID</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $licenses = $licenseManager->getAllLicenses();
                        foreach ($licenses as $license):
                            $status_class = 'status-' . $license['status'];
                            $created = date('M j, Y', strtotime($license['created_at']));
                            $expires = date('M j, Y', strtotime($license['expiry_date']));
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($license['user_key']) ?></strong></td>
                            <td><code><?= htmlspecialchars($license['hwid']) ?></code></td>
                            <td><?= $created ?></td>
                            <td><?= $expires ?></td>
                            <td><span class="<?= $status_class ?>"><?= ucfirst($license['status']) ?></span></td>
                            <td>
                                <a href="?action=delete&key=<?= urlencode($license['user_key']) ?>" 
                                   class="btn" 
                                   style="background: var(--danger); padding: 8px 12px; font-size: 0.9rem;"
                                   onclick="return confirm('Delete license for <?= htmlspecialchars($license['user_key']) ?>?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>