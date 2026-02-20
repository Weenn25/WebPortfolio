<div class="my-books-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-bookmark"></i> My Borrowed Books</h1>
    </div>

    <?php if(!empty($borrowed_books)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($borrowed_books as $borrow): ?>
                    <?php 
                        $is_overdue = (strtotime($borrow['due_date']) < time() && $borrow['status'] !== 'returned');
                        $days_overdue = $is_overdue ? floor((time() - strtotime($borrow['due_date'])) / 86400) : 0;
                    ?>
                    <tr <?= $is_overdue ? 'class="table-danger"' : '' ?>>
                        <td><strong><?= $borrow['book_title'] ?></strong></td>
                        <td><?= $borrow['author'] ?></td>
                        <td><?= date('M d, Y', strtotime($borrow['borrow_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($borrow['due_date'])) ?></td>
                        <td>
                            <?php if($borrow['status'] === 'returned'): ?>
                                <span class="badge bg-success">Returned</span>
                            <?php elseif($is_overdue): ?>
                                <span class="badge bg-danger">Overdue (<?= $days_overdue ?> days)</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Borrowed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= site_url('library/browse/view/' . $borrow['book_id']) ?>" class="btn btn-sm btn-info" title="View Book">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if($borrow['status'] !== 'returned'): ?>
                                <a href="<?= site_url('library/return/' . $borrow['id']) ?>" class="btn btn-sm btn-success" title="Return Book" onclick="return confirm('Are you sure you want to return this book?')">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> You haven't borrowed any books yet. <a href="<?= site_url('library/browse') ?>">Browse available books</a> to get started.
        </div>
    <?php endif; ?>
</div>

<style>
    .my-books-container {
        animation: fadeIn 0.5s ease-in;
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 700;
    }

    .header-line {
        border: 3px solid #3498db;
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
        background-color: #f8f9fa;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
