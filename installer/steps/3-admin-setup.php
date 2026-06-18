<?php
/**
 * Step 3: Admin Account Setup
 */

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../includes/functions.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $institutionName = trim($_POST['institution_name'] ?? '');
    $institutionNIT = trim($_POST['institution_nit'] ?? '');
    $institutionAddress = trim($_POST['institution_address'] ?? '');
    $institutionPhone = trim($_POST['institution_phone'] ?? '');
    $institutionEmail = trim($_POST['institution_email'] ?? '');
    $adminName = trim($_POST['admin_name'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPassword = $_POST['admin_password'] ?? '';
    $adminPasswordConfirm = $_POST['admin_password_confirmation'] ?? '';

    if (empty($institutionName)) $errors['institution_name'] = 'Institution name is required';
    if (empty($institutionEmail) || !filter_var($institutionEmail, FILTER_VALIDATE_EMAIL)) $errors['institution_email'] = 'Valid email is required';
    if (empty($adminName)) $errors['admin_name'] = 'Admin name is required';
    if (empty($adminEmail) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors['admin_email'] = 'Valid email is required';
    if (strlen($adminPassword) < 8) $errors['admin_password'] = 'Password must be at least 8 characters';
    if ($adminPassword !== $adminPasswordConfirm) $errors['admin_password_confirmation'] = 'Passwords do not match';

    if (empty($errors)) {
        $_SESSION['institution_name'] = $institutionName;
        $_SESSION['institution_nit'] = $institutionNIT;
        $_SESSION['institution_address'] = $institutionAddress;
        $_SESSION['institution_phone'] = $institutionPhone;
        $_SESSION['institution_email'] = $institutionEmail;
        $_SESSION['admin_name'] = $adminName;
        $_SESSION['admin_email'] = $adminEmail;
        $_SESSION['admin_password'] = $adminPassword;

        header('Location: index.php?step=4');
        exit;
    }
}
?>

<div class="card fade-in">
    <div class="card-header">
        <h2>&#128100; Admin Account Setup</h2>
        <p>Configure your institution and administrator account</p>
    </div>

    <form method="POST" action="index.php?step=3" data-validate>
        <div class="card-body">

            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">&#127963; Institution Information</h3>

            <div class="form-group">
                <label class="form-label" for="institution_name">Institution Name <span class="required">*</span></label>
                <input type="text" id="institution_name" name="institution_name" class="form-input" required
                       value="<?php echo htmlspecialchars($_SESSION['institution_name'] ?? ''); ?>"
                       placeholder="Hospital San Juan de Dios">
                <div class="form-error">Institution name is required</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="institution_nit">NIT / Tax ID</label>
                    <input type="text" id="institution_nit" name="institution_nit" class="form-input"
                           value="<?php echo htmlspecialchars($_SESSION['institution_nit'] ?? ''); ?>"
                           placeholder="900123456-7">
                </div>

                <div class="form-group">
                    <label class="form-label" for="institution_phone">Phone</label>
                    <input type="tel" id="institution_phone" name="institution_phone" class="form-input"
                           value="<?php echo htmlspecialchars($_SESSION['institution_phone'] ?? ''); ?>"
                           placeholder="+57 601 234 5678">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="institution_email">Institution Email <span class="required">*</span></label>
                    <input type="email" id="institution_email" name="institution_email" class="form-input" required
                           value="<?php echo htmlspecialchars($_SESSION['institution_email'] ?? ''); ?>"
                           placeholder="admin@hospital.com">
                    <div class="form-error">Valid email is required</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="institution_address">Address</label>
                    <input type="text" id="institution_address" name="institution_address" class="form-input"
                           value="<?php echo htmlspecialchars($_SESSION['institution_address'] ?? ''); ?>"
                           placeholder="Calle 123 # 45-67, Bogota">
                </div>
            </div>

            <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--border);">

            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">&#128101; Administrator Account</h3>

            <div class="form-group">
                <label class="form-label" for="admin_name">Full Name <span class="required">*</span></label>
                <input type="text" id="admin_name" name="admin_name" class="form-input" required
                       value="<?php echo htmlspecialchars($_SESSION['admin_name'] ?? ''); ?>"
                       placeholder="Dr. Juan Perez">
                <div class="form-error">Admin name is required</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="admin_email">Email <span class="required">*</span></label>
                <input type="email" id="admin_email" name="admin_email" class="form-input" required
                       value="<?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?>"
                       placeholder="admin@hospital.com">
                <div class="form-error">Valid email is required</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="admin_password">Password <span class="required">*</span></label>
                    <input type="password" id="admin_password" name="admin_password" class="form-input" required
                           minlength="8" placeholder="Minimum 8 characters">
                    <div class="form-error">Password must be at least 8 characters</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="admin_password_confirmation">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" class="form-input" required
                           placeholder="Re-enter password">
                    <div class="form-error">Passwords do not match</div>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <a href="index.php?step=2" class="btn btn-outline">
                <span class="btn-icon">&#8592;</span> Back
            </a>
            <button type="submit" class="btn btn-primary">
                Continue <span class="btn-icon">&#8594;</span>
            </button>
        </div>
    </form>
</div>
