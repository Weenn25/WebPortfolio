<div class="members-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-people"></i> Members Management</h1>
            <div>
                <a href="<?= site_url('library/members/inactive') ?>" class="btn btn-secondary">
                    <i class="bi bi-person-x"></i> View Inactive Members
                </a>
                <a href="<?= site_url('library/members/add') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Member
                </a>
            </div>
        </div>
    </div>



    <?php if(!empty($members)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Member Since</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $member): ?>
                    <tr>
                        <td><?= $member['id'] ?></td>
                        <td><strong><?= $member['first_name'] . ' ' . $member['last_name'] ?></strong></td>
                        <td><?= $member['email'] ?></td>
                        <td><?= $member['phone'] ?? 'N/A' ?></td>
                        <td><?= $member['address'] ?? 'N/A' ?></td>
                        <td><?= isset($member['created_at']) ? date('M d, Y', strtotime($member['created_at'])) : 'N/A' ?></td>
                        <td>
                            <?php if($member['is_active']): ?>
                                <span class="badge bg-success">ACTIVE</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">INACTIVE</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= site_url('library/members/view/' . $member['id']) ?>" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= site_url('library/members/edit/' . $member['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= site_url('library/members/deactivate/' . $member['id']) ?>" class="btn btn-sm btn-danger" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this member?')">
                                <i class="bi bi-lock"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No members found. <a href="#">Add a new member</a> to get started.
        </div>
    <?php endif; ?>
</div>

<style>
    .members-container {
        animation: fadeIn 0.5s ease-in;
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 700;
    }

    .header-line {
        border: 3px solid #3498db;
        margin-bottom: 30px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        font-weight: 600;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-sm {
        margin: 0 2px;
    }
</style>
