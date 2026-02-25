<div class="dashboard-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-speedometer2"></i> My Dashboard</h1>
    </div>

    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">My Borrowed Books</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-book" style="font-size: 3rem; color: #ffc107; margin-right: 15px;"></i>
                        <h2 class="stat-value text-warning" style="margin: 0;"><?= $borrowed_count ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Overdue Books</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history" style="font-size: 3rem; color: #dc3545; margin-right: 15px;"></i>
                        <h2 class="stat-value text-danger" style="margin: 0;"><?= $overdue_count ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Available Books</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-collection" style="font-size: 3rem; color: #28a745; margin-right: 15px;"></i>
                        <h2 class="stat-value text-success" style="margin: 0;"><?= $available_count ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-stack" style="font-size: 3rem; color: #17a2b8; margin-right: 15px;"></i>
                        <h2 class="stat-value text-info" style="margin: 0;"><?= $total_books ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <i class="bi bi-book"></i> Welcome to Library System
                </div>
                <div class="card-body">
                    <p>Hello, <strong><?= $user['first_name'] ?? 'User' ?></strong>!</p>
                    <p>You have access to our library system. Browse available books, check your borrowed items, and manage your account.</p>
                    <p class="text-muted">Use the menu on the left to navigate through different sections.</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <i class="bi bi-info-circle"></i> My Information
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?= $user['first_name'] . ' ' . $user['last_name'] ?></p>
                    <p><strong>Email:</strong> <?= $user['email'] ?></p>
                    <p><strong>Member Since:</strong> <?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?></p>
                    <hr>
                    <p class="text-muted small">Need help? Contact the library administration for assistance.</p>
                </div>
            </div>
        </div>
    </div>
</div>
