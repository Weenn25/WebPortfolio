<div class="book-details-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-book"></i> Book Details</h1>
        <a href="<?= site_url('library/browse') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Browse
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0"><?= $book['title'] ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Book ID:</th>
                            <td><strong>#<?= $book['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td><?= $book['title'] ?></td>
                        </tr>
                        <tr>
                            <th>Author:</th>
                            <td><?= $book['author'] ?></td>
                        </tr>
                        <tr>
                            <th>ISBN:</th>
                            <td><?= $book['isbn'] ?? '<em class="text-muted">Not provided</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Publisher:</th>
                            <td><?= $book['publisher'] ?? '<em class="text-muted">Not provided</em>' ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Publication Year:</th>
                            <td><?= $book['publication_year'] ?? '<em class="text-muted">Not provided</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Total Copies:</th>
                            <td><?= $book['total_quantity'] ?></td>
                        </tr>
                        <tr>
                            <th>Available Copies:</th>
                            <td><strong class="text-<?= $book['available_quantity'] > 0 ? 'success' : 'danger' ?>"><?= $book['available_quantity'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if($book['available_quantity'] > 0): ?>
                                    <span class="badge bg-success">Available</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if(!empty($book['description'])): ?>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h5>Description</h5>
                    <p class="text-muted"><?= nl2br($book['description']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <hr>

            <div class="d-flex justify-content-between">
                <div>
                    <?php if($book['available_quantity'] > 0): ?>
                        <a href="<?= site_url('library/borrow/' . $book['id']) ?>" class="btn btn-primary" onclick="return confirm('Are you sure you want to borrow this book?')">
                            <i class="bi bi-bookmark-plus"></i> Borrow This Book
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-bookmark-x"></i> Currently Unavailable
                        </button>
                    <?php endif; ?>
                </div>
                <a href="<?= site_url('library/browse') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Browse
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .book-details-container {
        animation: fadeIn 0.5s ease-in;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }

    .table th {
        font-weight: 600;
        color: #2c3e50;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
