<div class="member-view-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person"></i> Member Details</h1>
        <a href="<?= site_url('library/members') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if(!$member['is_active']): ?>
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> This member is currently <strong>INACTIVE</strong>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0"><?= $member['first_name'] . ' ' . $member['last_name'] ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Member ID:</th>
                            <td><strong>#<?= $member['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>First Name:</th>
                            <td><?= $member['first_name'] ?></td>
                        </tr>
                        <tr>
                            <th>Last Name:</th>
                            <td><?= $member['last_name'] ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= $member['email'] ?? '<em class="text-muted">Not provided</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><?= $member['phone'] ?? '<em class="text-muted">Not provided</em>' ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Membership Date:</th>
                            <td><?= date('F d, Y', strtotime($member['membership_date'])) ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if($member['is_active']): ?>
                                    <span class="badge bg-success">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">INACTIVE</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?= date('M d, Y g:i A', strtotime($member['created_at'])) ?></td>
                        </tr>
                        <?php if($member['updated_at']): ?>
                        <tr>
                            <th>Last Updated:</th>
                            <td><?= date('M d, Y g:i A', strtotime($member['updated_at'])) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <?php if(!empty($member['address'])): ?>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h5>Address</h5>
                    <p class="text-muted"><?= nl2br($member['address']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <hr>

            <div class="d-flex justify-content-between">
                <div>
                    <?php if($member['is_active']): ?>
                        <a href="<?= site_url('library/members/edit/' . $member['id']) ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Member
                        </a>
                        <a href="<?= site_url('library/members/deactivate/' . $member['id']) ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to deactivate this member?')">
                            <i class="bi bi-lock"></i> Deactivate
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('library/members/activate/' . $member['id']) ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this member?')">
                            <i class="bi bi-unlock"></i> Activate Member
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .member-view-container {
        animation: fadeIn 0.5s ease-in;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }

    .table th {
        font-weight: 600;
        color: #2c3e50;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
