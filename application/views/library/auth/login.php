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
                <!-- Notification container -->
                <div id="notificationContainer"></div>
                
                <!-- Hidden divs to store error and success messages for JavaScript use -->
                <div id="errorMessage" style="display:none;"><?= isset($error) && !empty($error) ? htmlspecialchars($error) : '' ?></div>
                <div id="successMessage" style="display:none;"><?php $success_msg = $this->session->flashdata('success'); echo isset($success_msg) && !empty($success_msg) ? htmlspecialchars($success_msg) : ''; ?></div>
                
                <?php $success_msg = $this->session->flashdata('success'); ?>
                <?php if (!empty($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="text-align: center; border-radius: 8px; border: 2px solid #28a745; margin-bottom: 25px;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <i class="bi bi-check-circle-fill" style="font-size: 24px; color: #28a745;"></i>
                            <div style="text-align: center;">
                                <strong style="font-size: 16px; color: #155724; display: block;">Success!</strong>
                                <span style="color: #155724; font-size: 14px;"><?= htmlspecialchars($success_msg) ?></span>
                            </div>
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

                    <button type="submit" class="btn btn-primary btn-login" id="loginBtn">
                        <span id="loginBtnText">Login</span>
                        <i class="bi bi-arrow-right-circle"></i>
                        <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </form>

                <div class="auth-divider">
                    <span>OR</span>
                </div>
                <div class="auth-footer">
                    <p>Don't have an account? <a href="<?= site_url('library/register') ?>">Create Account</a></p>
                    <p class="forgot-password"><a href="<?= site_url('library/forgot-password') ?>">Forgot Password?</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to show notification
        function showNotification(message, type = 'error') {
            const container = document.getElementById('notificationContainer');
            const icon = type === 'error' ? 'exclamation-circle-fill' : 'check-circle-fill';
            
            const notificationHTML = `
                <div class="notification ${type}">
                    <i class="bi bi-${icon}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            container.innerHTML = notificationHTML;
        }

        // Generate unique tab ID for this login session
        // IMPORTANT: Always generate a fresh tab_id on login page to prevent conflicts
        // when opening new tabs from existing ones (sessionStorage gets copied)
        // Generate ONCE and return the same ID on subsequent calls to avoid mismatches
        let _generatedTabId = null; // Cache to ensure same tab_id is used throughout login
        
        function getTabId() {
            if (_generatedTabId) {
                // Return cached tab_id to ensure consistency
                return _generatedTabId;
            }
            
            // Generate a completely new tab_id for this login attempt
            _generatedTabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('tabId', _generatedTabId);
            sessionStorage.setItem('currentTabId', _generatedTabId);
            window.name = _generatedTabId;
            return _generatedTabId;
        }

        // Add tab ID to login form before submission
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginSpinner = document.getElementById('loginSpinner');
            
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
                        tabIdInput.value = getTabId();  // Use cached tab_id
                        this.appendChild(tabIdInput);
                    }
                    
                    // Show loading spinner
                    loginBtnText.style.display = 'none';
                    loginSpinner.style.display = 'inline-block';
                    loginBtn.disabled = true;
                });
            }
        });

        // Intercept all fetch requests to add tab ID header
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            const tabId = sessionStorage.getItem('currentTabId');
            if (tabId) {
                if (args[1] === undefined) {
                    args[1] = {};
                }
                if (!args[1].headers) {
                    args[1].headers = {};
                }
                args[1].headers['X-Tab-Id'] = tabId;
            }
            return originalFetch.apply(this, args);
        };

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

        // Show notifications on page load
        window.addEventListener('load', function() {
            document.querySelector('.auth-card').classList.add('loaded');
            
            // Check error message from hidden div
            const errorMsg = document.getElementById('errorMessage').textContent.trim();
            if (errorMsg) {
                showNotification(errorMsg, 'error');
                // If there's an error, hide spinner and re-enable button
                const loginBtn = document.getElementById('loginBtn');
                const loginBtnText = document.getElementById('loginBtnText');
                const loginSpinner = document.getElementById('loginSpinner');
                if (loginBtn) {
                    loginBtnText.style.display = 'inline';
                    loginSpinner.style.display = 'none';
                    loginBtn.disabled = false;
                }
            }
        });
    </script>
</body>
</html>
