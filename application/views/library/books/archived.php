<div class="archived-books-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-archive"></i> Archived Books</h1>
            <a href="<?= site_url('library/books') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Active Books
            </a>
        </div>
    </div>

    <?php if(!empty($books)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Total Copies</th>
                        <th>Available</th>
                        <th>Borrowed</th>
                        <th>Archived Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($books as $book): ?>
                    <?php $borrowed = $book['total_quantity'] - $book['available_quantity']; ?>
                    <tr style="background-color: #f8f9fa;">
                        <td><?= $book['id'] ?></td>
                        <td><strong><?= $book['title'] ?></strong></td>
                        <td><?= $book['author'] ?></td>
                        <td><?= $book['isbn'] ?? 'N/A' ?></td>
                        <td><?= $book['total_quantity'] ?? 0 ?></td>
                        <td><?= $book['available_quantity'] ?? 0 ?></td>
                        <td>
                            <?php if ($borrowed > 0): ?>
                                <span class="badge bg-warning text-dark"><?= $borrowed ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">0</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $book['updated_at'] ? date('M d, Y', strtotime($book['updated_at'])) : 'N/A' ?></td>
                        <td>
                            <a href="<?= site_url('library/books/view/' . $book['id']) ?>" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if ($borrowed == 0): ?>
                                <a href="<?= site_url('library/books/restore/' . $book['id']) ?>" class="btn btn-sm btn-success" title="Restore" onclick="return confirm('Are you sure you want to restore this book?')">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" title="Cannot restore - has borrowed copies" disabled>
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No archived books found. All books are currently active.
        </div>
    <?php endif; ?>
</div>

<style>
    .archived-books-container {
        animation: fadeIn 0.5s ease-in;
    }

    .page-header h1 {
        color: #495057;
        font-weight: 700;
    }

    .header-line {
        border: 3px solid #6c757d;
        margin-bottom: 30px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        font-weight: 600;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #e9ecef !important;
    }

    .btn-sm {
        margin: 0 2px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
