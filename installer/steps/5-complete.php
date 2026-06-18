<?php
/**
 * Step 5: Installation Complete
 */

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../includes/functions.php';
}

$serverInfo = getServerInfo();
$config = getConfig();
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminEmail = $_SESSION['admin_email'] ?? 'admin@hospital.com';
$institutionName = $_SESSION['institution_name'] ?? 'Hospital';
?>

<div class="card fade-in">
    <div class="card-body" style="text-align: center; padding: 48px 32px;">
        <div class="success-icon">&#10003;</div>

        <div class="success-message">
            <h2>Installation Complete!</h2>
            <p>Your Hospital EMR/EHR system has been installed successfully.</p>
        </div>

        <div class="credentials-box" style="text-align: left;">
            <h4>Login Credentials</h4>
            <div class="credential-item">
                <span class="label">Admin Name</span>
                <span class="value"><?php echo htmlspecialchars($adminName); ?></span>
            </div>
            <div class="credential-item">
                <span class="label">Admin Email</span>
                <span class="value"><?php echo htmlspecialchars($adminEmail); ?></span>
            </div>
            <div class="credential-item">
                <span class="label">Password</span>
                <span class="value">Use the password you set during installation</span>
            </div>
        </div>

        <div class="warning-box">
            <span class="icon">&#9888;</span>
            <div class="text">
                <strong>Important: Delete the installer directory</strong>
                For security reasons, you should delete the <code>/installer</code> directory from your server immediately after confirming the system works.
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px;">
            <a href="/login" class="btn btn-primary">
                <span class="btn-icon">&#128274;</span> Go to Login
            </a>
            <a href="/" class="btn btn-outline">
                <span class="btn-icon">&#127968;</span> Go to Homepage
            </a>
        </div>

        <div class="info-grid" style="margin-top: 40px; text-align: left;">
            <div class="info-item">
                <div class="label">Institution</div>
                <div class="value"><?php echo htmlspecialchars($institutionName); ?></div>
            </div>
            <div class="info-item">
                <div class="label">PHP Version</div>
                <div class="value"><?php echo htmlspecialchars($serverInfo['php_version']); ?></div>
            </div>
            <div class="info-item">
                <div class="label">Server</div>
                <div class="value"><?php echo htmlspecialchars($serverInfo['server_software']); ?></div>
            </div>
            <div class="info-item">
                <div class="label">Operating System</div>
                <div class="value"><?php echo htmlspecialchars($serverInfo['os']); ?></div>
            </div>
        </div>
    </div>
</div>
