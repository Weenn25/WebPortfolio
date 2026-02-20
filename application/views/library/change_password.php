<h1 class="mb-4">
    <i class="bi bi-key"></i> Change Password
</h1>

<script>
    function handlePasswordChange(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        fetch('<?= site_url('library/change-password-ajax') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                iziToast.success({
                    title: 'Success',
                    message: data.message,
                    position: 'topRight',
                    timeout: 3000
                });

                // Clear the form
                document.getElementById('passwordForm').reset();
            } else {
                // Show error notification
                iziToast.error({
                    title: 'Error',
                    message: data.message,
                    position: 'topRight',
                    timeout: 4000
                });
            }
        })
        .catch(error => {
            iziToast.error({
                title: 'Error',
                message: 'An error occurred. Please try again.',
                position: 'topRight',
                timeout: 4000
            });
        });
    }
</script>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-form-check"></i> Update Your Password</h5>
            </div>
            <div class="card-body">
                <form id="passwordForm" onsubmit="handlePasswordChange(event)">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <small class="text-muted">Enter your current password for verification</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="text-muted">Password must be at least 6 characters long</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <small class="text-muted">Re-enter your new password to confirm</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Change Password
                        </button>
                        <a href="<?= site_url('library/profile') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Password Security</h5>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Password Requirements:</strong></p>
                <ul class="small">
                    <li>Minimum 6 characters</li>
                    <li>Use a mix of letters and numbers</li>
                    <li>Avoid using personal information</li>
                    <li>Use unique passwords for different accounts</li>
                </ul>
                <p class="small mt-3 mb-0">
                    <strong>Tip:</strong> Change your password regularly to keep your account secure.
                </p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> Important</h5>
            </div>
            <div class="card-body">
                <p class="small">
                    Your password will be updated immediately. You'll need to use your new password on your next login.
                </p>
            </div>
        </div>
    </div>
</div>
