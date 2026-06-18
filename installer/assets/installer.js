/**
 * Hospital EMR/EHR Installer - JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initForms();
    initDatabaseTest();
    initInstallProcess();
    scrollToErrors();
});

/* ===== FORM VALIDATION ===== */

function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                return false;
            }
        });

        const inputs = form.querySelectorAll('.form-input[required]');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                validateField(input);
            });
            input.addEventListener('input', function() {
                clearFieldError(input);
            });
        });
    });
}

function validateForm(form) {
    let valid = true;
    const inputs = form.querySelectorAll('.form-input[required]');

    inputs.forEach(function(input) {
        if (!validateField(input)) {
            valid = false;
        }
    });

    const password = form.querySelector('#admin_password');
    const confirm = form.querySelector('#admin_password_confirmation');
    if (password && confirm) {
        if (password.value !== confirm.value) {
            showFieldError(confirm, 'Passwords do not match');
            valid = false;
        }
    }

    if (!valid) {
        const firstError = form.querySelector('.form-error.visible');
        if (firstError) {
            firstError.closest('.form-group').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    return valid;
}

function validateField(input) {
    const value = input.value.trim();
    let valid = true;
    let message = '';

    if (input.hasAttribute('required') && !value) {
        valid = false;
        message = 'This field is required';
    } else if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            valid = false;
            message = 'Please enter a valid email address';
        }
    } else if (input.dataset.minlength && value.length < parseInt(input.dataset.minlength)) {
        valid = false;
        message = 'Minimum ' + input.dataset.minlength + ' characters required';
    }

    if (!valid) {
        showFieldError(input, message);
    } else {
        clearFieldError(input);
    }

    return valid;
}

function showFieldError(input, message) {
    input.classList.add('error');
    const errorEl = input.parentElement.querySelector('.form-error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('visible');
    }
}

function clearFieldError(input) {
    input.classList.remove('error');
    const errorEl = input.parentElement.querySelector('.form-error');
    if (errorEl) {
        errorEl.classList.remove('visible');
    }
}

/* ===== DATABASE CONNECTION TEST ===== */

function initDatabaseTest() {
    const testBtn = document.getElementById('testConnectionBtn');
    if (!testBtn) return;

    testBtn.addEventListener('click', function() {
        testDatabaseConnection();
    });
}

function testDatabaseConnection() {
    const btn = document.getElementById('testConnectionBtn');
    const resultEl = document.getElementById('testResult');
    const form = document.getElementById('databaseForm');

    const host = document.getElementById('db_host').value.trim() || 'localhost';
    const port = document.getElementById('db_port').value.trim() || '3306';
    const name = document.getElementById('db_name').value.trim();
    const user = document.getElementById('db_user').value.trim();
    const pass = document.getElementById('db_password').value;

    if (!name || !user) {
        resultEl.className = 'test-result error';
        resultEl.innerHTML = '<strong>Error:</strong> Please fill in database name and username.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Testing...';
    resultEl.className = 'test-result testing';
    resultEl.innerHTML = '<span class="spinner"></span> Testing database connection...';
    resultEl.style.display = 'block';

    const formData = new FormData();
    formData.append('action', 'test_connection');
    formData.append('db_host', host);
    formData.append('db_port', port);
    formData.append('db_name', name);
    formData.append('db_user', user);
    formData.append('db_password', pass);

    fetch('index.php?step=2', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            resultEl.className = 'test-result success';
            resultEl.innerHTML = '<strong>Success!</strong> ' + data.message;
        } else {
            resultEl.className = 'test-result error';
            resultEl.innerHTML = '<strong>Error:</strong> ' + data.message;
        }
    })
    .catch(function(error) {
        resultEl.className = 'test-result error';
        resultEl.innerHTML = '<strong>Error:</strong> Connection failed. Please try again.';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<span class="btn-icon">&#128269;</span> Test Connection';
    });
}

/* ===== INSTALLATION PROCESS ===== */

function initInstallProcess() {
    const installContainer = document.getElementById('installSteps');
    if (!installContainer) return;

    runInstallStep(0);
}

function runInstallStep(stepIndex) {
    const steps = document.querySelectorAll('.install-step');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const totalSteps = steps.length;

    if (stepIndex >= totalSteps) {
        window.location.href = 'index.php?step=5';
        return;
    }

    steps.forEach(function(s, i) {
        s.classList.remove('active');
        if (i < stepIndex) s.classList.add('completed');
    });

    steps[stepIndex].classList.add('active');
    steps[stepIndex].querySelector('.install-step-icon').innerHTML = '<span class="spinner"></span>';

    const progress = Math.round((stepIndex / totalSteps) * 100);
    if (progressFill) progressFill.style.width = progress + '%';
    if (progressText) progressText.textContent = progress + '%';

    const formData = new FormData();
    formData.append('action', 'install_step');
    formData.append('step', stepIndex);

    fetch('index.php?step=4', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            steps[stepIndex].classList.remove('active');
            steps[stepIndex].classList.add('completed');
            steps[stepIndex].querySelector('.install-step-icon').innerHTML = '&#10003;';

            const logOutput = document.getElementById('logOutput');
            if (logOutput && data.log) {
                logOutput.innerHTML += '<span class="info">[Step ' + (stepIndex + 1) + ']</span> ' + data.log + '\n';
                logOutput.scrollTop = logOutput.scrollHeight;
            }

            const newProgress = Math.round(((stepIndex + 1) / totalSteps) * 100);
            if (progressFill) {
                progressFill.style.width = newProgress + '%';
                if (stepIndex + 1 === totalSteps) {
                    progressFill.classList.add('success');
                }
            }
            if (progressText) progressText.textContent = newProgress + '%';

            setTimeout(function() {
                runInstallStep(stepIndex + 1);
            }, 500);
        } else {
            steps[stepIndex].classList.remove('active');
            steps[stepIndex].classList.add('failed');
            steps[stepIndex].querySelector('.install-step-icon').innerHTML = '&#10007;';

            if (progressFill) {
                progressFill.classList.add('error');
            }

            const logOutput = document.getElementById('logOutput');
            if (logOutput) {
                logOutput.innerHTML += '<span class="error">[Error]</span> ' + (data.message || 'Unknown error occurred') + '\n';
                if (data.log) {
                    logOutput.innerHTML += data.log + '\n';
                }
                logOutput.scrollTop = logOutput.scrollHeight;
            }

            const errorBox = document.getElementById('installError');
            if (errorBox) {
                errorBox.style.display = 'block';
                errorBox.querySelector('.error-message').textContent = data.message || 'Installation failed at this step.';
            }

            const retryBtn = document.getElementById('retryBtn');
            if (retryBtn) {
                retryBtn.style.display = 'inline-flex';
            }
        }
    })
    .catch(function(error) {
        steps[stepIndex].classList.remove('active');
        steps[stepIndex].classList.add('failed');
        steps[stepIndex].querySelector('.install-step-icon').innerHTML = '&#10007;';

        const logOutput = document.getElementById('logOutput');
        if (logOutput) {
            logOutput.innerHTML += '<span class="error">[Network Error]</span> ' + error.message + '\n';
            logOutput.scrollTop = logOutput.scrollHeight;
        }

        const errorBox = document.getElementById('installError');
        if (errorBox) {
            errorBox.style.display = 'block';
            errorBox.querySelector('.error-message').textContent = 'Network error: ' + error.message;
        }

        const retryBtn = document.getElementById('retryBtn');
        if (retryBtn) {
            retryBtn.style.display = 'inline-flex';
        }
    });
}

function retryInstall() {
    const steps = document.querySelectorAll('.install-step');
    steps.forEach(function(s) {
        s.classList.remove('active', 'completed', 'failed');
        s.querySelector('.install-step-icon').innerHTML = '';
    });

    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    if (progressFill) {
        progressFill.style.width = '0%';
        progressFill.classList.remove('error', 'success');
    }
    if (progressText) progressText.textContent = '0%';

    const errorBox = document.getElementById('installError');
    if (errorBox) errorBox.style.display = 'none';

    const retryBtn = document.getElementById('retryBtn');
    if (retryBtn) retryBtn.style.display = 'none';

    const logOutput = document.getElementById('logOutput');
    if (logOutput) logOutput.innerHTML = '';

    runInstallStep(0);
}

/* ===== UTILITIES ===== */

function scrollToErrors() {
    const firstError = document.querySelector('.form-error.visible, .requirement-item.failed');
    if (firstError) {
        setTimeout(function() {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
