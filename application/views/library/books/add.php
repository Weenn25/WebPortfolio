<div class="book-add-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-circle"></i> Add New Book</h1>
        <a href="<?= site_url('library/books') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>



    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0">New Book Information</h4>
        </div>
        <div class="card-body">
            <form action="<?= base_url('library/insert_book') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="isbn" name="isbn" value="<?= set_value('isbn') ?>" required>
                            <small class="text-muted">e.g., 978-0-06-112008-4</small>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= set_value('title') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" value="<?= set_value('author') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" value="<?= set_value('publisher') ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="publication_year" class="form-label">Publication Year</label>
                            <input type="number" class="form-control" id="publication_year" name="publication_year" value="<?= set_value('publication_year') ?>" min="1000" max="<?= date('Y') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="total_quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_quantity" name="total_quantity" value="<?= set_value('total_quantity', 1) ?>" required min="1">
                            <small class="text-muted">Number of copies to add</small>
                        </div>

                        <div class="mb-3">
                            <label for="available_quantity" class="form-label">Available Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="available_quantity" name="available_quantity" value="<?= set_value('available_quantity', 1) ?>" required min="0">
                            <small class="text-muted">Must be less than or equal to total quantity</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= set_value('description') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Add Book
                    </button>
                    <a href="<?= site_url('library/books') ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .book-add-container {
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

<script>
    // Validate available quantity doesn't exceed total quantity
    document.getElementById('available_quantity').addEventListener('input', function() {
        const totalQty = parseInt(document.getElementById('total_quantity').value) || 0;
        const availQty = parseInt(this.value) || 0;
        
        if (availQty > totalQty) {
            this.setCustomValidity('Available quantity cannot exceed total quantity');
        } else {
            this.setCustomValidity('');
        }
    });

    document.getElementById('total_quantity').addEventListener('input', function() {
        const availQty = parseInt(document.getElementById('available_quantity').value) || 0;
        const totalQty = parseInt(this.value) || 0;
        
        // Auto-update available quantity if it exceeds new total
        if (availQty > totalQty) {
            document.getElementById('available_quantity').value = totalQty;
        }
        
        // Trigger validation
        const availInput = document.getElementById('available_quantity');
        availInput.dispatchEvent(new Event('input'));
    });
</script>
