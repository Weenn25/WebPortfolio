<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Management System - Login</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/library.css') ?>">
</head>
<body class="auth-page">
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
                        <i class="bi bi-book-fill"></i>
                    </div>
                </div>
                <h2 class="auth-title">Library Management</h2>
                
            </div>
            <div class="auth-form">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div class="alert-content">
                            <strong>Oops!</strong>
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" class="auth-form-inputs">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-fill"></i> Username
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Enter your username" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required autocomplete="current-password">
                            <button class="password-toggle" type="button" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span>Login</span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    <span>OR</span>
                </div>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="<?= site_url('library/register') ?>">Create Account</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate unique tab ID for this login session
        function getTabId() {
            let tabId = sessionStorage.getItem('tabId');
            if (!tabId) {
                tabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('tabId', tabId);
            }
            return tabId;
        }

        // Add tab ID to login form before submission
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    // Check if tab_id field already exists
                    let hasTabId = false;
                    this.querySelectorAll('input[name="tab_id"]').forEach(() => {
                        hasTabId = true;
                    });
                    
                    if (!hasTabId) {
                        const tabIdInput = document.createElement('input');
                        tabIdInput.type = 'hidden';
                        tabIdInput.name = 'tab_id';
                        tabIdInput.value = getTabId();
                        this.appendChild(tabIdInput);
                    }
                });
            }
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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

        // Add fade-in animation on load
        window.addEventListener('load', function() {
            document.querySelector('.auth-card').classList.add('loaded');
            
            // Auto-hide error alert after 1 second
            const alert = document.querySelector('.auth-form .alert');
            if (alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.maxHeight = '0px';
                    alert.style.marginBottom = '0px';
                    alert.style.padding = '0px';
                    alert.style.overflow = 'hidden';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 400);
                }, 2000);
            }
        });
    </script>
</body>
</html>
