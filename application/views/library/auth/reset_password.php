<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Library Management System</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/library.css') ?>">
</head>
<body class="auth-page">
    <div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1050;\"></div>
    <div class="auth-background">
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header text-center">
                <div class="auth-icon-wrapper">
                    <div class="auth-icon-circle">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                </div>
                <h2 class="auth-title">Reset Password</h2>
                <p class="auth-subtitle">Enter your new password below</p>
            </div>
            <div class="auth-form">

                <form method="post" class="auth-form-inputs">
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i> New Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter new password (min 6 characters)" required minlength="6">
                            <button class="password-toggle" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="bi bi-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Must be at least 6 characters long</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm" class="form-label">
                            <i class="bi bi-lock-fill"></i> Confirm Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="Confirm your new password" required minlength="6">
                            <button class="password-toggle" type="button" onclick="togglePassword('password_confirm', 'toggleIcon2')">
                                <i class="bi bi-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span>Reset Password</span>
                        <i class="bi bi-check-circle"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p><a href="<?= site_url('library/login') ?>"><i class="bi bi-arrow-left"></i> Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show error notification using Bootstrap Toast
        function showErrorNotification(message) {
            const container = document.getElementById('notificationContainer');
            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-danger text-white">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            // Remove after close
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Validate passwords match before submit
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($error)): ?>
                showErrorNotification('<?= htmlspecialchars($error) ?>');
            <?php endif; ?>

            // Handle form submission to validate passwords match
            const resetForm = document.querySelector('form');
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const passwordConfirm = document.getElementById('password_confirm').value;
                    
                    if (password !== passwordConfirm) {
                        e.preventDefault();
                        showErrorNotification('Passwords do not match. Please try again.');
                        return false;
                    }
                });
            }
        });

        // Add fade-in animation on load
        window.addEventListener('load', function() {
            document.querySelector('.auth-card').classList.add('loaded');
        });
    </script>
</body>
</html>
