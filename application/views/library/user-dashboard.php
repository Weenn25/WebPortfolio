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

<style>
    .dashboard-container {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .header-line {
        border: 3px solid #3498db;
        margin-bottom: 30px;
    }

    .stat-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card .card-title {
        color: #7f8c8d;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 1.8rem;
            margin-bottom: 12px;
        }

        .stat-card .card-title {
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 2rem;
        }

        .stat-card .d-flex i {
            font-size: 2rem !important;
            margin-right: 10px !important;
        }

        .card-header {
            font-size: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-body p {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .page-header h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .stat-card .card-title {
            font-size: 0.75rem;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 1.8rem;
        }

        .stat-card .d-flex i {
            font-size: 1.8rem !important;
            margin-right: 8px !important;
        }

        .stat-card .card-body {
            padding: 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .card-body p {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .row.mb-4 {
            margin-bottom: 1rem !important;
        }

        .col-md-6.col-lg-3.mb-3 {
            margin-bottom: 0.75rem !important;
        }
    }
</style>
