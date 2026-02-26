<div class="book-view-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-book"></i> Book Details</h1>
        <div>
            <?php if ($book['archived']): ?>
                <button class="btn btn-success" onclick="restoreBook(<?= $book['id'] ?>)">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore Book
                </button>
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
