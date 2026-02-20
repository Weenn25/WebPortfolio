<div class="book-edit-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil"></i> Edit Book</h1>
        <a href="<?= site_url('library/books') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0">Edit Book Information</h4>
        </div>
        <div class="card-body">
            <form action="<?= site_url('library/books/update/' . $book['id']) ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="isbn" name="isbn" value="<?= $book['isbn'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= $book['title'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" value="<?= $book['author'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" value="<?= $book['publisher'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="publication_year" class="form-label">Publication Year</label>
                            <input type="number" class="form-control" id="publication_year" name="publication_year" value="<?= $book['publication_year'] ?? '' ?>" min="1000" max="<?= date('Y') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="total_quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="total_quantity" name="total_quantity" value="<?= $book['total_quantity'] ?>" required min="0">
                        </div>

                        <div class="mb-3">
                            <label for="available_quantity" class="form-label">Available Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="available_quantity" name="available_quantity" value="<?= $book['available_quantity'] ?>" required min="0">
                            <small class="text-muted">Must be less than or equal to total quantity</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= $book['description'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Update Book
                    </button>
                    <div>
                        <a href="<?= site_url('library/books/view/' . $book['id']) ?>" class="btn btn-info">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="<?= site_url('library/books') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .book-edit-container {
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
        const availInput = document.getElementById('available_quantity');
        availInput.dispatchEvent(new Event('input'));
    });
</script>
