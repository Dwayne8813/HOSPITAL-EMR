<?php
/**
 * Step 2: Database Configuration
 */

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../includes/functions.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'test_connection') {
        $host = trim($_POST['db_host'] ?? 'localhost');
        $port = (int) ($_POST['db_port'] ?? 3306);
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? '');
        $pass = $_POST['db_password'] ?? '';

        if (empty($name) || empty($user)) {
            echo json_encode(['success' => false, 'message' => 'Database name and username are required.']);
            exit;
        }

        $result = testDatabaseConnection($host, $port, $name, $user, $pass);
        echo json_encode($result);
        exit;
    }

    if ($_POST['action'] === 'save_database') {
        $_SESSION['db_host'] = trim($_POST['db_host'] ?? 'localhost');
        $_SESSION['db_port'] = (int) ($_POST['db_port'] ?? 3306);
        $_SESSION['db_name'] = trim($_POST['db_name'] ?? '');
        $_SESSION['db_user'] = trim($_POST['db_user'] ?? '');
        $_SESSION['db_password'] = $_POST['db_password'] ?? '';

        header('Location: index.php?step=3');
        exit;
    }
}

$host = $_SESSION['db_host'] ?? 'localhost';
$port = $_SESSION['db_port'] ?? '3306';
$name = $_SESSION['db_name'] ?? '';
$user = $_SESSION['db_user'] ?? '';
$pass = $_SESSION['db_password'] ?? '';
?>

<div class="card fade-in">
    <div class="card-header">
        <h2>&#128451; Database Configuration</h2>
        <p>Enter your MySQL database connection details</p>
    </div>

    <form id="databaseForm" method="POST" action="index.php?step=2">
        <input type="hidden" name="action" value="save_database">

        <div class="card-body">
            <div class="alert alert-info">
                <span>&#8505;</span>
                <span>Create a database in your Hostinger control panel (hPanel) under Databases &gt; MySQL Databases before proceeding.</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" class="form-input"
                           value="<?php echo htmlspecialchars($host); ?>" placeholder="localhost">
                    <div class="form-hint">Usually "localhost" on Hostinger</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="db_port">Port</label>
                    <input type="number" id="db_port" name="db_port" class="form-input"
                           value="<?php echo htmlspecialchars($port); ?>" placeholder="3306">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="db_name">Database Name <span class="required">*</span></label>
                <input type="text" id="db_name" name="db_name" class="form-input" required
                       value="<?php echo htmlspecialchars($name); ?>" placeholder="u123456789_hospital">
                <div class="form-hint">Enter the full database name from Hostinger</div>
                <div class="form-error">Database name is required</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="db_user">Username <span class="required">*</span></label>
                    <input type="text" id="db_user" name="db_user" class="form-input" required
                           value="<?php echo htmlspecialchars($user); ?>" placeholder="u123456789_admin">
                    <div class="form-error">Username is required</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="db_password">Password <span class="required">*</span></label>
                    <input type="password" id="db_password" name="db_password" class="form-input" required
                           value="<?php echo htmlspecialchars($pass); ?>" placeholder="Enter database password">
                    <div class="form-error">Password is required</div>
                </div>
            </div>

            <button type="button" id="testConnectionBtn" class="btn btn-outline btn-sm">
                <span class="btn-icon">&#128269;</span> Test Connection
            </button>

            <div id="testResult" class="test-result"></div>
        </div>

        <div class="card-footer">
            <a href="index.php?step=1" class="btn btn-outline">
                <span class="btn-icon">&#8592;</span> Back
            </a>
            <button type="submit" class="btn btn-primary">
                Continue <span class="btn-icon">&#8594;</span>
            </button>
        </div>
    </form>
</div>
