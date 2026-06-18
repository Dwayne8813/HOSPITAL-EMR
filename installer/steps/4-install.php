<?php
/**
 * Step 4: Installation Process
 */

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../includes/functions.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install_step') {
    header('Content-Type: application/json');
    $installStep = (int) ($_POST['step'] ?? 0);

    $config = getConfig();
    $log = '';

    switch ($installStep) {
        case 0:
            $result = createEnvFile($config);
            if ($result) {
                $log = 'Environment file created successfully.';
                echo json_encode(['success' => true, 'log' => $log]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create .env file. Check directory permissions.']);
            }
            exit;

        case 1:
            $result = generateKey();
            if ($result['success']) {
                $log = 'Application key generated.';
                echo json_encode(['success' => true, 'log' => $log]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to generate application key.', 'log' => $result['output']]);
            }
            exit;

        case 2:
            $result = runMigrations();
            if ($result['success']) {
                $log = 'Database migrations completed successfully.';
                echo json_encode(['success' => true, 'log' => $log]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database migration failed.',
                    'log' => $result['output']
                ]);
            }
            exit;

        case 3:
            $result = runSeeders();
            if ($result['success']) {
                $log = 'Database seeders executed successfully.';
                echo json_encode(['success' => true, 'log' => $log]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database seeding failed.',
                    'log' => $result['output']
                ]);
            }
            exit;

        case 4:
            $permResults = setPermissions();
            $allGood = true;
            foreach ($permResults as $pr) {
                if (!$pr['success']) $allGood = false;
            }

            if ($allGood) {
                $log = 'Directory permissions set successfully.';
                echo json_encode(['success' => true, 'log' => $log]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to set some directory permissions.',
                    'log' => print_r($permResults, true)
                ]);
            }
            exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown installation step.']);
    exit;
}
?>

<div class="card fade-in">
    <div class="card-header">
        <h2>&#9881; Installing System</h2>
        <p>Please wait while the system is being installed. Do not close this page.</p>
    </div>

    <div class="card-body">
        <div class="progress-container">
            <div class="progress-label">
                <span>Installation Progress</span>
                <span id="progressText">0%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        <div class="install-steps" id="installSteps">
            <div class="install-step" data-step="0">
                <div class="install-step-icon">1</div>
                <div class="install-step-text">
                    <h4>Create Configuration File</h4>
                    <p>Generating .env file with your settings</p>
                </div>
            </div>

            <div class="install-step" data-step="1">
                <div class="install-step-icon">2</div>
                <div class="install-step-text">
                    <h4>Generate Application Key</h4>
                    <p>Creating encryption key for security</p>
                </div>
            </div>

            <div class="install-step" data-step="2">
                <div class="install-step-icon">3</div>
                <div class="install-step-text">
                    <h4>Run Database Migrations</h4>
                    <p>Creating database tables and structure</p>
                </div>
            </div>

            <div class="install-step" data-step="3">
                <div class="install-step-icon">4</div>
                <div class="install-step-text">
                    <h4>Seed Database</h4>
                    <p>Populating initial data and admin account</p>
                </div>
            </div>

            <div class="install-step" data-step="4">
                <div class="install-step-icon">5</div>
                <div class="install-step-text">
                    <h4>Set Permissions</h4>
                    <p>Configuring directory access permissions</p>
                </div>
            </div>
        </div>

        <div id="installError" class="alert alert-error" style="margin-top: 20px; display: none;">
            <span>&#9888;</span>
            <div>
                <strong>Installation Error</strong>
                <div class="error-message"></div>
            </div>
        </div>

        <button id="retryBtn" class="btn btn-danger btn-sm" style="display: none; margin-top: 12px;" onclick="retryInstall()">
            <span class="btn-icon">&#8635;</span> Retry Installation
        </button>

        <div class="log-output" id="logOutput"></div>
    </div>
</div>
