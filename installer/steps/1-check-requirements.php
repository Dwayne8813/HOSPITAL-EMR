<?php
/**
 * Step 1: Check Server Requirements
 */

if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../includes/functions.php';
}

$requirements = getRequirements();
$failedRequired = false;
$warnings = [];

foreach ($requirements as $req) {
    if (!$req['passed'] && empty($req['optional'])) {
        $failedRequired = true;
    }
    if (!$req['passed'] && !empty($req['optional'])) {
        $warnings[] = $req;
    }
}

$canProceed = !$failedRequired;
?>

<div class="card fade-in">
    <div class="card-header">
        <h2>&#9881; Server Requirements Check</h2>
        <p>Verifying that your server meets all necessary requirements</p>
    </div>

    <div class="card-body">
        <?php if ($canProceed): ?>
        <div class="alert alert-success">
            <span>&#10003;</span>
            <span>All requirements are met. You can proceed to the next step.</span>
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <span>&#9888;</span>
            <span>Some requirements are not met. Please fix the issues below or click "Retry" to check again.</span>
        </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
        <div class="alert alert-warning">
            <span>&#9888;</span>
            <span><?php echo count($warnings); ?> optional <?php echo count($warnings) === 1 ? 'requirement has' : 'requirements have'; ?> warnings. These are not blocking but may affect functionality.</span>
        </div>
        <?php endif; ?>

        <div class="requirements-list">
            <?php foreach ($requirements as $req): ?>
            <div class="requirement-item <?php echo $req['passed'] ? 'passed' : (empty($req['optional']) ? 'failed' : 'warning'); ?>">
                <div class="requirement-name">
                    <?php if ($req['passed']): ?>
                        <span>&#10003;</span>
                    <?php elseif (!empty($req['optional'])): ?>
                        <span>&#9888;</span>
                    <?php else: ?>
                        <span>&#10007;</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($req['name']); ?>
                    <?php if (!empty($req['optional'])): ?>
                        <span style="font-size:11px; color: var(--text-muted);">(optional)</span>
                    <?php endif; ?>
                </div>
                <div class="requirement-status">
                    <?php if ($req['passed']): ?>
                        OK
                    <?php elseif (!empty($req['optional'])): ?>
                        Warning
                    <?php else: ?>
                        Failed
                    <?php endif; ?>
                    <span class="requirement-value"><?php echo htmlspecialchars($req['current']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card-footer">
        <a href="index.php?step=1" class="btn btn-outline">
            <span class="btn-icon">&#8635;</span> Retry
        </a>
        <?php if ($canProceed): ?>
        <a href="index.php?step=2" class="btn btn-primary">
            Continue <span class="btn-icon">&#8594;</span>
        </a>
        <?php else: ?>
        <button class="btn btn-primary" disabled>
            Fix issues to continue
        </button>
        <?php endif; ?>
    </div>
</div>
