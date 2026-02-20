<div class="member-add-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person-plus"></i> Add New Member</h1>
        <a href="<?= site_url('library/members') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>



    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0">New Member Information</h4>
        </div>
        <div class="card-body">
            <form action="<?= site_url('library/members/insert') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= set_value('first_name') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= set_value('last_name') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email') ?>">
                            <small class="text-muted">Optional - for notifications</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= set_value('phone') ?>">
                            <small class="text-muted">e.g., 555-0123</small>
                        </div>

                        <div class="mb-3">
                            <label for="membership_date" class="form-label">Membership Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="membership_date" name="membership_date" value="<?= set_value('membership_date', date('Y-m-d')) ?>" required max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?= set_value('address') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Add Member
                    </button>
                    <a href="<?= site_url('library/members') ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .member-add-container {
        animation: fadeIn 0.5s ease-in;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
