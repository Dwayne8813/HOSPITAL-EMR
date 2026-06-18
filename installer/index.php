<?php
/**
 * Hospital EMR/EHR System - Web Installer
 * Main Entry Point
 */

require_once __DIR__ . '/includes/functions.php';

$step = getStep();
$totalSteps = 5;

if ($step < 1 || $step > $totalSteps) {
    $step = 1;
}

if ($step > 1 && empty($_SESSION['db_host'])) {
    header('Location: index.php?step=2');
    exit;
}

if (isInstalled() && $step !== 5) {
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Hospital EMR/EHR - Installer</title>
    <link rel="stylesheet" href="assets/installer.css">
</head>
<body>

<header class="installer-header">
    <div class="header-content">
        <div class="header-icon">&#9764;</div>
        <div class="header-text">
            <h1>Hospital EMR/EHR System</h1>
            <p>Web Installation Wizard v<?php echo INSTALLER_VERSION; ?></p>
        </div>
    </div>
</header>

<nav class="step-indicator">
    <div class="steps">
        <?php for ($i = 1; $i <= $totalSteps; $i++): ?>
        <div class="step <?php echo $i === $step ? 'active' : ($i < $step ? 'completed' : ''); ?>">
            <div class="step-number">
                <?php if ($i < $step): ?>
                    &#10003;
                <?php else: ?>
                    <?php echo $i; ?>
                <?php endif; ?>
            </div>
            <span class="step-label">
                <?php
                $labels = ['Requirements', 'Database', 'Admin Account', 'Install', 'Complete'];
                echo $labels[$i - 1];
                ?>
            </span>
        </div>
        <?php endfor; ?>
    </div>
</nav>

<main class="installer-container">
    <?php
    $stepFile = __DIR__ . '/steps/' . $step . '-' . [
        '',
        'check-requirements',
        'database-config',
        'admin-setup',
        'install',
        'complete'
   ][$step] . '.php';

    if (file_exists($stepFile)) {
        require $stepFile;
    }
    ?>
</main>

<script src="assets/installer.js"></script>
</body>
</html>
