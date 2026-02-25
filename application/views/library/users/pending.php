<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-check"></i> Pending User Approvals</h2>
        <a href="<?= site_url('library/approved-users') ?>" class="btn btn-outline-primary">
            <i class="bi bi-people-fill"></i> View Approved Users
        </a>
    </div>

    <?php if (!empty($pending_users)): ?>
        <div class="card">
            <div class="card-header bg-primary bg-opacity-10">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    <h5 class="mb-0">Pending Registrations (<?= count($pending_users) ?>)</h5>
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
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-secondary text-white">
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
                                    <td class="text-center">
                                        <a href="<?= site_url('library/approve-user/' . $user['id']) ?>" 
                                           class="btn btn-success btn-sm me-1"
                                           onclick="return confirm('Are you sure you want to approve this user?');"
                                           title="Approve">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </a>
                                        <a href="<?= site_url('library/reject-user/' . $user['id']) ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to reject and remove this user? This action cannot be undone.');"
                                           title="Reject">
                                            <i class="bi bi-x-lg"></i> Reject
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
               
                <h4 class="mt-3 text-muted">No Pending Registrations</h4>
                <p class="text-muted">All user registrations have been processed. New registrations will appear here.</p>
            </div>
        </div>
    <?php endif; ?>
</div>
