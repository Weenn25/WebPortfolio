<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people-fill"></i> Approved Users</h2>
        <a href="<?= site_url('library/pending-users') ?>" class="btn btn-outline-primary">
            <i class="bi bi-person-check"></i> View Pending Users
            <?php 
            $pending_count = $this->Library_model->count_pending_users();
            if ($pending_count > 0): ?>
                <span class="badge bg-danger rounded-pill ms-1"><?= $pending_count ?></span>
            <?php endif; ?>
        </a>
    </div>

    <?php if (!empty($approved_users)): ?>
        <div class="card">
            <div class="card-header bg-success bg-opacity-10">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle text-success"></i>
                    <h5 class="mb-0">Active Users (<?= count($approved_users) ?>)</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Registered On</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approved_users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-primary text-white">
                                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code><?= htmlspecialchars($user['username']) ?></code></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?php 
                                        try {
                                            // Parse database time as UTC, convert to Asia/Manila (UTC+8)
                                            $dt = new DateTime($user['created_at'], new DateTimeZone('UTC'));
                                            $dt->setTimezone(new DateTimeZone('Asia/Manila'));
                                            echo $dt->format('M d, Y h:i A');
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($user['created_at']);
                                        }
                                    ?></td>
                                    <td>
                                        <span class="badge bg-success px-3 py-2">Active</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('library/deactivate-user/' . $user['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to deactivate this user? They will no longer be able to log in.');"
                                           title="Deactivate">
                                            <i class="bi bi-person-x"></i> Deactivate
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3 text-muted">No Approved Users</h4>
                <p class="text-muted">There are no approved member users yet.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
}
</style>
