<h1 class="mb-4">
    <i class="bi bi-person-circle"></i> My Profile
</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                <?php if (isset($user) && $user): ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">First Name</h6>
                            <p><?= htmlspecialchars($user['first_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Last Name</h6>
                            <p><?= htmlspecialchars($user['last_name']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Username</h6>
                            <p><?= htmlspecialchars($user['username']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Email</h6>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Role</h6>
                            <p>
                                <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <p>
                                <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Member Since</h6>
                            <p><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Last Updated</h6>
                            <p><?= $user['updated_at'] ? date('M d, Y', strtotime($user['updated_at'])) : 'N/A' ?></p>
                        </div>
                    </div>
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
                <h5 class="mb-0"><i class="bi bi-shield-check"></i> Account Settings</h5>
            </div>
            <div class="card-body">
                <a href="<?= site_url('library/edit-profile') ?>" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </a>
                <a href="<?= site_url('library/change-password') ?>" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-key"></i> Change Password
                </a>
                <a href="<?= site_url('library/logout') ?>" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-question-circle"></i> Help & Support</h5>
            </div>
            <div class="card-body">
                <p class="small">Need help? Contact the system administrator.</p>
                <ul class="small">
                    <li>Report a bug</li>
                    <li>Request a feature</li>
                    <li>Get support</li>
                </ul>
            </div>
        </div>
    </div>
</div>
