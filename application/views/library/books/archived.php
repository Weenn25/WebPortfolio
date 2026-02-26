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
                                <button class="btn btn-sm btn-success" title="Restore" onclick="restoreBook(<?= $book['id'] ?>)">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
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

<script>
function restoreBook(bookId) {
    iziToast.show({
        timeout: 20000,
        layout: 2,
        title: '<i class="bi bi-arrow-counterclockwise"></i> Restore Book',
        message: 'Are you sure you want to restore this book?',
        position: 'center',
        backgroundColor: '#3498db',
        titleColor: '#fff',
        messageColor: '#fff',
        titleFontSize: '18px',
        messageFontSize: '15px',
        padding: '20px',
        progressBar: true,
        progressBarColor: '#fff',
        icon: false,
        maxWidth: '500px',
        animateInside: true,
        transitionIn: 'fadeInDown',
        transitionOut: 'fadeOutUp',
        zindex: 9999,
        overlay: true,
        buttons: [
            ['<button class="btn btn-light btn-sm" style="font-weight: 600; padding: 10px 24px; border: none; cursor: pointer; touch-action: auto;"><i class="bi bi-check-circle"></i> YES, RESTORE</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
                window.location.href = '<?= site_url("library/books/restore/") ?>' + bookId;
            }, true],
            ['<button class="btn btn-outline-light btn-sm" style="font-weight: 600; padding: 10px 24px; border-width: 2px; cursor: pointer; touch-action: auto;"><i class="bi bi-x-circle"></i> CANCEL</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
            }]
        ]
    });
}
</script>
