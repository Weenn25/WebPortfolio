<div class="history-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-clock-history"></i> Borrowing History</h1>
    </div>

    <?php if(!empty($history)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $record): ?>
                    <?php 
                        $is_overdue = ($record['status'] === 'borrowed' && strtotime($record['due_date']) < time());
                    ?>
                    <tr>
                        <td><strong><?= $record['book_title'] ?></strong></td>
                        <td><?= $record['author'] ?></td>
                        <td><?= date('M d, Y', strtotime($record['borrow_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($record['due_date'])) ?></td>
                        <td>
                            <?php if($record['return_date']): ?>
                                <?= date('M d, Y', strtotime($record['return_date'])) ?>
                            <?php else: ?>
                                <em class="text-muted">Not returned</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($record['status'] === 'returned'): ?>
                                <span class="badge bg-success">Returned</span>
                            <?php elseif($is_overdue): ?>
                                <span class="badge bg-danger">Overdue</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Borrowed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info view-book-btn" data-book-id="<?= $record['book_id'] ?>" title="View Book">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if($record['status'] !== 'returned'): ?>
                                <button class="btn btn-sm btn-success" onclick="returnBook(<?= $record['id'] ?>)" title="Return Book">
                                    <i class="bi bi-arrow-return-left"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if(!empty($pagination) && $pagination['total_pages'] > 1): ?>
            <nav class="mt-3" aria-label="Borrowing history pagination">
                <ul class="pagination justify-content-center">
                    <?php
                        $current_page = $pagination['current_page'];
                        $total_pages = $pagination['total_pages'];
                        $base_url = $pagination['base_url'];
                        $prev_page = $current_page - 1;
                        $next_page = $current_page + 1;
                    ?>

                    <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $current_page <= 1 ? '#' : $base_url . '?page=' . $prev_page ?>" aria-label="Previous">
                            &laquo;
                        </a>
                    </li>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $base_url . '?page=' . $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $current_page >= $total_pages ? '#' : $base_url . '?page=' . $next_page ?>" aria-label="Next">
                            &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <div class="mt-4">
            <div class="card">
                <div class="card-body">
                    <h5>Quick Summary</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Total Borrowed:</strong> <?= $history_total ?? count($history) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Returned:</strong> <?= $history_returned ?? count(array_filter($history, function($h) { return $h['status'] === 'returned'; })) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Currently Borrowed:</strong> <?= $history_borrowed ?? count(array_filter($history, function($h) { return $h['status'] === 'borrowed'; })) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> You don't have any borrowing history yet. <a href="<?= site_url('library/browse') ?>">Browse available books</a> to start borrowing.
        </div>
    <?php endif; ?>
</div>

<!-- View Book Modal -->
<div class="modal fade" id="viewBookModal" tabindex="-1" aria-labelledby="viewBookLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="viewBookLabel"><i class="bi bi-book"></i> Book Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewBookContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle View Book button clicks
    document.querySelectorAll('.view-book-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            viewBookDetails(bookId);
        });
    });
});

function viewBookDetails(bookId) {
    const modal = new bootstrap.Modal(document.getElementById('viewBookModal'));
    const contentDiv = document.getElementById('viewBookContent');
    
    fetch('<?= site_url("library/get_book_details_ajax/") ?>' + bookId)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success && data.book) {
                const book = data.book;
                contentDiv.innerHTML = `
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Book ID:</th>
                            <td><strong>#${book.id}</strong></td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td>${book.title}</td>
                        </tr>
                        <tr>
                            <th>Author:</th>
                            <td>${book.author}</td>
                        </tr>
                        <tr>
                            <th>Publisher:</th>
                            <td>${book.publisher || '<em class="text-muted">Not provided</em>'}</td>
                        </tr>
                        <tr>
                            <th>Publication Year:</th>
                            <td>${book.publication_year || '<em class="text-muted">Not provided</em>'}</td>
                        </tr>
                        <tr>
                            <th>Total Copies:</th>
                            <td>${book.total_quantity}</td>
                        </tr>
                        <tr>
                            <th>Available Copies:</th>
                            <td><strong class="text-${book.available_quantity > 0 ? 'success' : 'danger'}">${book.available_quantity}</strong></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                ${book.available_quantity > 0 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Out of Stock</span>'}
                            </td>
                        </tr>
                        ${book.description ? `<tr><th>Description:</th><td>${book.description}</td></tr>` : ''}
                    </table>
                `;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading book details</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading book details</div>';
            iziToast.error({title: 'Error', message: 'Failed to load book details'});
        });
    
    modal.show();
}

function returnBook(borrowId) {
    iziToast.show({
        timeout: 20000,
        layout: 2,
        title: '<i class="bi bi-arrow-return-left"></i> Return Book',
        message: 'Are you sure you want to return this book?',
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
            ['<button class="btn btn-light btn-sm" style="font-weight: 600; padding: 10px 24px; border: none; cursor: pointer; touch-action: auto;"><i class="bi bi-check-circle"></i> YES, RETURN</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
                window.location.href = '<?= site_url("library/return/") ?>' + borrowId;
            }, true],
            ['<button class="btn btn-outline-light btn-sm" style="font-weight: 600; padding: 10px 24px; border-width: 2px; cursor: pointer; touch-action: auto;"><i class="bi bi-x-circle"></i> CANCEL</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
            }]
        ]
    });
}
</script>
