<div class="dashboard-container">
    <!-- Pending Approvals Alert -->
    <?php if (isset($pending_count) && $pending_count > 0): ?>
    <div class="alert alert-primary alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-person-exclamation me-3" style="font-size: 1.5rem;"></i>
        <div class="flex-grow-1">
            <strong><?= $pending_count ?> pending user registration<?= $pending_count > 1 ? 's' : '' ?></strong> awaiting your approval.
        </div>
        <a href="<?= site_url('library/pending-users') ?>" class="btn btn-primary btn-sm ms-3">
            <i class="bi bi-arrow-right"></i> Review Now
        </a>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stat-cards-row mb-5">
        <div class="stat-card-wrapper">
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

        <div class="stat-card-wrapper">
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

        <div class="stat-card-wrapper">
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

        <div class="stat-card-wrapper">
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

        <!-- Overdue Books Alert Card - Same Row -->
        <div class="stat-card-wrapper">
            <div class="stat-card stat-card--overdue">
                <div class="stat-card__icon">
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

    <!-- Overdue Books Section -->
    <?php if (!empty($overdue_books)): ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header dashboard-card-header bg-danger text-white">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h5 class="mb-0">Overdue Books (<?= count($overdue_books) ?>)</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Book Title</th>
                                <th>Member Name</th>
                                <th>Member Email</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Days Overdue</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($overdue_books as $book): ?>
                            <tr class="table-danger">
                                <td><strong><?= htmlspecialchars($book['title']) ?></strong></td>
                                <td><?= htmlspecialchars($book['first_name'] . ' ' . $book['last_name']) ?></td>
                                <td><?= htmlspecialchars($book['email']) ?></td>
                                <td><?= date('M d, Y', strtotime($book['borrow_date'])) ?></td>
                                <td><?= date('M d, Y', strtotime($book['due_date'])) ?></td>
                                <td>
                                    <span class="badge bg-danger">
                                        <?= (int)((strtotime(date('Y-m-d')) - strtotime($book['due_date'])) / 86400) ?> days
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('library/circulation') ?>" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
                        <p class="info-value" id="currentDateTime"><?= date('M d, Y, h:i A') ?></p>
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
                    <p class="text-muted small">Last updated: <span id="lastUpdated">Just now</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time clock for System Information
function updateDateTime() {
    const now = new Date();
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    };
    const dateTimeString = now.toLocaleDateString('en-US', options);
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        dateTimeElement.textContent = dateTimeString;
    }
    
    // Update "Last updated" timestamp
    const lastUpdatedElement = document.getElementById('lastUpdated');
    if (lastUpdatedElement) {
        lastUpdatedElement.textContent = 'Just now';
    }
}

// Update immediately on load
updateDateTime();

// Update every second
setInterval(updateDateTime, 1000);
</script>
