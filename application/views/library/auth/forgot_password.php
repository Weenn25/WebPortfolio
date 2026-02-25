<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - Library Management System</title>
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
                        <i class="bi bi-key-fill"></i>
                    </div>
                </div>
                <h2 class="auth-title">Forgot Password</h2>
                <p class="auth-subtitle">Enter your email to receive a password reset link</p>
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

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div class="alert-content">
                            <strong>Success!</strong>
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" class="auth-form-inputs" id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill"></i> Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Enter your registered email" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login" id="submitBtn">
                        <span id="btnText">Send Reset Link</span>
                        <i class="bi bi-arrow-right-circle" id="btnIcon"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    <span>OR</span>
                </div>

                <div class="auth-footer">
                    <p>Remember your password? <a href="<?= site_url('library/login') ?>">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add fade-in animation on load
        window.addEventListener('load', function() {
            document.querySelector('.auth-card').classList.add('loaded');
            
            // Auto-hide alert only for error messages after 3 seconds
            const errorAlert = document.querySelector('.auth-form .alert-danger');
            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.style.opacity = '0';
                    errorAlert.style.maxHeight = '0px';
                    errorAlert.style.marginBottom = '0px';
                    errorAlert.style.padding = '0px';
                    errorAlert.style.overflow = 'hidden';
                    setTimeout(function() {
                        errorAlert.style.display = 'none';
                    }, 400);
                }, 3000);
            }
            
            // Keep success alerts visible
            const successAlert = document.querySelector('.auth-form .alert-success');
            if (successAlert) {
                // Do not auto-hide success alerts
            }
        });

        // Handle form submission with loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Add loading state
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.8';
                    
                    // Update button text and icon
                    btnText.innerHTML = 'Sending to Gmail';
                    btnIcon.className = 'bi bi-hourglass-split';
                    
                    // Add animation to icon
                    btnIcon.style.animation = 'spin 1s linear infinite';
                    
                    // Keep button disabled during submission
                    // Form will naturally submit and redirect
                });
            }
        });
    </script>
</body>
</html>
