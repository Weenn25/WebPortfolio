<div class="book-view-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-book"></i> Book Details</h1>
        <div>
            <?php if ($book['archived']): ?>
                <a href="<?= site_url('library/books/restore/' . $book['id']) ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to restore this book?')">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore Book
                </a>
            <?php else: ?>
                <a href="<?= site_url('library/books/edit/' . $book['id']) ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Book
                </a>
            <?php endif; ?>
            <a href="<?= site_url('library/books' . ($book['archived'] ? '/archived' : '')) ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <?php if ($book['archived']): ?>
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <strong>This book is archived.</strong> It will not appear in the active books list.
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
            <h4 class="mb-0"><?= $book['title'] ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">ID:</th>
                            <td><?= $book['id'] ?></td>
                        </tr>
                        <tr>
                            <th>ISBN:</th>
                            <td><?= $book['isbn'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td><strong><?= $book['title'] ?></strong></td>
                        </tr>
                        <tr>
                            <th>Author:</th>
                            <td><?= $book['author'] ?></td>
                        </tr>
                        <tr>
                            <th>Publisher:</th>
                            <td><?= $book['publisher'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Publication Year:</th>
                            <td><?= $book['publication_year'] ?? 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Total Quantity:</th>
                            <td><span class="badge bg-primary"><?= $book['total_quantity'] ?></span></td>
                        </tr>
                        <tr>
                            <th>Available Quantity:</th>
                            <td><span class="badge bg-success"><?= $book['available_quantity'] ?></span></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($book['available_quantity'] > 0): ?>
                                    <span class="badge bg-success">Available</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Archived:</th>
                            <td>
                                <?php if ($book['archived']): ?>
                                    <span class="badge bg-secondary">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-success">No</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td><?= date('M d, Y h:i A', strtotime($book['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td><?= $book['updated_at'] ? date('M d, Y h:i A', strtotime($book['updated_at'])) : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($book['description']): ?>
                <hr>
                <h5>Description:</h5>
                <p class="text-muted"><?= nl2br($book['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .book-view-container {
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
