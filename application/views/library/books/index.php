<div class="books-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-book"></i> Books Management</h1>
            <div>
                <a href="<?= site_url('library/books/archived') ?>" class="btn btn-secondary">
                    <i class="bi bi-archive"></i> View Archived Books
                </a>
                <a href="<?= site_url('library/books/add') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Book
                </a>
            </div>
        </div>
    </div>



    <?php if(!empty($books)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Quantity</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><strong><?= $book['title'] ?></strong></td>
                        <td><?= $book['author'] ?></td>
                        <td><?= $book['total_quantity'] ?? 0 ?></td>
                        <td><?= $book['available_quantity'] ?? 0 ?></td>
                        <td>
                            <?php 
                            $available = $book['available_quantity'] ?? 0;
                            $status = $available > 0 ? 'Available' : 'Out of Stock';
                            $badge_class = $status === 'Available' ? 'success' : 'danger';
                            ?>
                            <span class="badge bg-<?= $badge_class ?>"><?= $status ?></span>
                        </td>
                        <td>
                            <a href="<?= site_url('library/books/view/' . $book['id']) ?>" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= site_url('library/books/edit/' . $book['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= site_url('library/books/archive/' . $book['id']) ?>" class="btn btn-sm btn-danger" title="Archive" onclick="return confirm('Are you sure you want to archive this book?')">
                                <i class="bi bi-archive"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No books found. <a href="#">Add a new book</a> to get started.
        </div>
    <?php endif; ?>
</div>
