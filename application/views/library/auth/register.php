<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Management System - Register</title>
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
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                </div>
                <h2 class="auth-title">Create Account</h2>
                <p class="auth-subtitle">Join our library community</p>
            </div>
            <div class="auth-form">
                <div id="form-message" class="alert" style="display: none;" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div class="alert-content">
                        <strong id="message-title">Error</strong>
                        <p id="message-text"></p>
                    </div>
                </div>

                <form id="register-form" class="auth-form-inputs">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="form-label">
                                    <i class="bi bi-person-fill"></i> First Name
                                </label>
                                <div class="input-group">
                                    <span class="input-group-icon">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           required value="<?= set_value('first_name') ?>">
                                </div>
                                <?php if (form_error('first_name')): ?>
                                    <div class="form-error"><?= form_error('first_name') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="form-label">
                                    <i class="bi bi-person-fill"></i> Last Name
                                </label>
                                <div class="input-group">
                                    <span class="input-group-icon">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           required value="<?= set_value('last_name') ?>">
                                </div>
                                <?php if (form_error('last_name')): ?>
                                    <div class="form-error"><?= form_error('last_name') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-badge"></i> Username
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-at"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   required value="<?= set_value('username') ?>">
                        </div>
                        <div id="username-feedback" class="form-feedback"></div>
                        <?php if (form_error('username')): ?>
                            <div class="form-error"><?= form_error('username') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill"></i> Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-icon">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   required value="<?= set_value('email') ?>">
                        </div>
                        <div id="email-feedback" class="form-feedback"></div>
                        <?php if (form_error('email')): ?>
                            <div class="form-error"><?= form_error('email') ?></div>
                        <?php endif; ?>
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
                                   required>
                            <button class="password-toggle" type="button" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="bi bi-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <?php if (form_error('password')): ?>
                            <div class="form-error"><?= form_error('password') ?></div>
                        <?php endif; ?>
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
                                   required>
                            <button class="password-toggle" type="button" onclick="togglePassword('password_confirm', 'toggleIcon2')">
                                <i class="bi bi-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                        <?php if (form_error('password_confirm')): ?>
                            <div class="form-error"><?= form_error('password_confirm') ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <span>Create Account</span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    <span>OR</span>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="<?= site_url('library/login') ?>">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Add fade-in animation on load
        window.addEventListener('load', function() {
            document.querySelector('.auth-card').classList.add('loaded');
        });

        // Handle form submission via AJAX
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById('form-message');
            const messageTitle = document.getElementById('message-title');
            const messageText = document.getElementById('message-text');
            const submitBtn = this.querySelector('button[type="submit"]');
            const btnText = submitBtn.querySelector('span');
            const originalText = btnText.textContent;
            
            // Disable submit button
            submitBtn.disabled = true;
            btnText.textContent = 'Creating...';
            
            fetch('<?= site_url("library/register_ajax") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.classList.remove('alert-danger');
                    messageDiv.classList.add('alert-success');
                    messageTitle.textContent = 'Success';
                    messageText.textContent = data.message;
                    
                    // Change icon to check mark
                    const icon = messageDiv.querySelector('i');
                    icon.classList.remove('bi-exclamation-circle-fill');
                    icon.classList.add('bi-check-circle-fill');
                    
                    messageDiv.style.display = 'flex';
                    
                    // Clear form
                    document.getElementById('register-form').reset();
                    
                    // Redirect to login after 2 seconds
                    setTimeout(() => {
                        window.location.href = '<?= site_url("library/login") ?>';
                    }, 2000);
                } else {
                    messageDiv.classList.remove('alert-success');
                    messageDiv.classList.add('alert-danger');
                    
                    // Change icon back to exclamation mark
                    const icon = messageDiv.querySelector('i');
                    icon.classList.remove('bi-check-circle-fill');
                    icon.classList.add('bi-exclamation-circle-fill');
                    messageTitle.textContent = 'Registration Error';
                    messageText.textContent = data.message;
                    messageDiv.style.display = 'flex';
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    btnText.textContent = originalText;
                    
                    // Auto-hide error after 3 seconds
                    setTimeout(function() {
                        messageDiv.style.opacity = '0';
                        messageDiv.style.maxHeight = '0px';
                        messageDiv.style.marginBottom = '0px';
                        messageDiv.style.padding = '0px';
                        messageDiv.style.overflow = 'hidden';
                        setTimeout(function() {
                            messageDiv.style.display = 'none';
                            messageDiv.style.opacity = '1';
                            messageDiv.style.maxHeight = '500px';
                            messageDiv.style.marginBottom = '1.5rem';
                            messageDiv.style.padding = '1rem 1.2rem';
                        }, 400);
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.classList.remove('alert-success');
                messageDiv.classList.add('alert-danger');
                messageTitle.textContent = 'Error';
                messageText.textContent = 'An error occurred. Please try again.';
                messageDiv.style.display = 'flex';
                
                // Re-enable submit button
                submitBtn.disabled = false;
                btnText.textContent = originalText;
            });
        });

        // Debounce function to limit API calls
        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Get CSRF token
        function getCsrfToken() {
            const name = '<?= $this->security->get_csrf_token_name() ?>';
            const value = document.querySelector('input[name="' + name + '"]')?.value;
            return { name: name, value: value };
        }

        // Real-time username availability check
        const checkUsername = debounce(function(username) {
            const feedback = document.getElementById('username-feedback');
            
            if (username.length < 3) {
                feedback.innerHTML = '';
                return;
            }
            
            const formData = new FormData();
            formData.append('username', username);
            
            // Add CSRF token if it exists
            const csrf = getCsrfToken();
            if (csrf.value) {
                formData.append(csrf.name, csrf.value);
            }
            
            fetch('<?= site_url("library/check_username") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedback.innerHTML = '<i class="bi bi-x-circle"></i> Username already exists';
                    feedback.className = 'form-feedback feedback-error';
                } else {
                    feedback.innerHTML = '<i class="bi bi-check-circle"></i> Username available';
                    feedback.className = 'form-feedback feedback-success';
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
                feedback.innerHTML = '';
            });
        }, 500);

        document.getElementById('username').addEventListener('input', function() {
            checkUsername(this.value.trim());
        });

        // Real-time email availability check
        const checkEmail = debounce(function(email) {
            const feedback = document.getElementById('email-feedback');
            
            if (!email || !email.includes('@')) {
                feedback.innerHTML = '';
                return;
            }
            
            const formData = new FormData();
            formData.append('email', email);
            
            // Add CSRF token if it exists
            const csrf = getCsrfToken();
            if (csrf.value) {
                formData.append(csrf.name, csrf.value);
            }
            
            fetch('<?= site_url("library/check_email") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    feedback.innerHTML = '<i class="bi bi-x-circle"></i> Email already exists';
                    feedback.className = 'form-feedback feedback-error';
                } else {
                    feedback.innerHTML = '<i class="bi bi-check-circle"></i> Email available';
                    feedback.className = 'form-feedback feedback-success';
                }
            })
            .catch(error => {
                console.error('Error checking email:', error);
                feedback.innerHTML = '';
            });
        }, 500);

        document.getElementById('email').addEventListener('input', function() {
            checkEmail(this.value.trim());
        });
    </script>
</body>
</html>
