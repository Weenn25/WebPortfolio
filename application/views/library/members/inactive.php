<div class="members-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-person-x"></i> Inactive Members</h1>
            <a href="<?= site_url('library/members') ?>" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Back to Active Members
            </a>
        </div>
    </div>

    <?php if(!empty($members)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Member Since</th>
                        <th>Deactivated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $member): ?>
                    <tr style="background-color: #f8f9fa;">
                        <td><?= $member['id'] ?></td>
                        <td><strong><?= $member['first_name'] . ' ' . $member['last_name'] ?></strong></td>
                        <td><?= $member['email'] ?? 'N/A' ?></td>
                        <td><?= $member['phone'] ?? 'N/A' ?></td>
                        <td><?= $member['address'] ?? 'N/A' ?></td>
                        <td><?= isset($member['created_at']) ? date('M d, Y', strtotime($member['created_at'])) : 'N/A' ?></td>
                        <td><?= $member['updated_at'] ? date('M d, Y', strtotime($member['updated_at'])) : 'N/A' ?></td>
                        <td>
                            <a href="<?= site_url('library/members/view/' . $member['id']) ?>" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= site_url('library/members/activate/' . $member['id']) ?>" class="btn btn-sm btn-success" title="Activate" onclick="return confirm('Are you sure you want to activate this member?')">
                                <i class="bi bi-unlock"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No inactive members found. All members are currently active.
        </div>
    <?php endif; ?>
</div>

<style>
    .members-container {
        animation: fadeIn 0.5s ease-in;
    }

    .page-header h1 {
        color: #6c757d;
        font-weight: 700;
    }

    .header-line {
        border: 3px solid;
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
        background-color: #e9ecef !important;
    }

    .btn-sm {
        margin: 0 2px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
