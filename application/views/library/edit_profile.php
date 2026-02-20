<h1 class="mb-4">
    <i class="bi bi-pencil-square"></i> Edit Profile
</h1>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($this->session->flashdata('success')): ?>
            iziToast.success({
                title: 'Success',
                message: '<?= $this->session->flashdata('success') ?>',
                position: 'topRight',
                timeout: 3000,
                displayMode: 'replace'
            });
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
            iziToast.error({
                title: 'Error',
                message: '<?= $this->session->flashdata('error') ?>',
                position: 'topRight',
                timeout: 4000,
                displayMode: 'replace'
            });
        <?php endif; ?>
    });
</script>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-form-check"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($user) && $user): ?>
                    <form method="post" action="<?= site_url('library/edit-profile') ?>">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            <?php if (form_error('first_name')): ?>
                                <small class="text-danger"><?= form_error('first_name') ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            <?php if (form_error('last_name')): ?>
                                <small class="text-danger"><?= form_error('last_name') ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                            <?php if (form_error('email')): ?>
                                <small class="text-danger"><?= form_error('email') ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" 
                                   value="<?= ucfirst($user['role']) ?>" disabled>
                            <small class="text-muted">Role is assigned by administrator</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="<?= site_url('library/profile') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No profile information available.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Information</h5>
            </div>
            <div class="card-body">
                <p class="small mb-3">
                    <strong>Note:</strong> You can update your first name, last name, and email address. 
                    Your username and role cannot be changed.
                </p>
                <p class="small mb-0">
                    To change your password, visit the <a href="<?= site_url('library/change-password') ?>">Change Password</a> page.
                </p>
            </div>
        </div>
    </div>
</div>
