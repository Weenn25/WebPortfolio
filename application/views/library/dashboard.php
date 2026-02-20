<div class="dashboard-container">
    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card stat-card--total">
                <div class="stat-card__icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="stat-card__content">
                    <h5 class="stat-card__label">Total Books</h5>
                    <div class="stat-card__number"><?= $stats['total_books'] ?? 0 ?></div>
                    <p class="stat-card__meta">Total copies in library</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card stat-card--available">
                <div class="stat-card__icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-card__content">
                    <h5 class="stat-card__label">Available Books</h5>
                    <div class="stat-card__number"><?= $stats['available_books'] ?? 0 ?></div>
                    <p class="stat-card__meta">Ready to borrow</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card stat-card--members">
                <div class="stat-card__icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-card__content">
                    <h5 class="stat-card__label">Total Members</h5>
                    <div class="stat-card__number"><?= $stats['total_members'] ?? 0 ?></div>
                    <p class="stat-card__meta">Active members</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card stat-card--borrowed">
                <div class="stat-card__icon">
                    <i class="bi bi-hand-index"></i>
                </div>
                <div class="stat-card__content">
                    <h5 class="stat-card__label">Borrowed Books</h5>
                    <div class="stat-card__number"><?= $stats['borrowed_books'] ?? 0 ?></div>
                    <p class="stat-card__meta">Currently borrowed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Books -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="stat-card stat-card--overdue">
                <div class="stat-card__icon-large">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-card__content">
                    <h5 class="stat-card__label">Overdue Books</h5>
                    <div class="stat-card__number"><?= $stats['overdue_books'] ?? 0 ?></div>
                    <p class="stat-card__meta">Books past due date</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome and Info Sections -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card dashboard-card">
                <div class="card-header dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i>
                        <h5 class="mb-0">System Overview</h5>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        You have full access to manage the library system. Use the menu on the left to navigate through different sections.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="overview-item">
                                <i class="bi bi-book-half"></i>
                                <div>
                                    <h6>Manage Books</h6>
                                    <p>Add, edit, and archive books</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="overview-item">
                                <i class="bi bi-people-fill"></i>
                                <div>
                                    <h6>Manage Members</h6>
                                    <p>Manage library members</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="overview-item">
                                <i class="bi bi-arrow-left-right"></i>
                                <div>
                                    <h6>Track Circulation</h6>
                                    <p>Monitor book borrowing and returns</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="overview-item">
                                <i class="bi bi-graph-up"></i>
                                <div>
                                    <h6>View Reports</h6>
                                    <p>Access system statistics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card dashboard-card">
                <div class="card-header dashboard-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event"></i>
                        <h5 class="mb-0">System Information</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="info-label">Current Date</label>
                        <p class="info-value"><?= date('M d, Y, h:i A') ?></p>
                    </div>
                    <div class="info-item mb-3">
                        <label class="info-label">Your Role</label>
                        <p class="info-value">
                            <span class="badge bg-primary px-3 py-2"><?= ucfirst($this->session->userdata('library_role')) ?></span>
                        </p>
                    </div>
                    <div class="info-item">
                        <label class="info-label">Status</label>
                        <p class="info-value">
                            <span class="badge bg-success px-3 py-2">Active</span>
                        </p>
                    </div>
                    <hr class="my-3">
                    <p class="text-muted small">Last updated: Just now</p>
                </div>
            </div>
        </div>
    </div>
</div>
