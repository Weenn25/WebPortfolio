<div class="my-books-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-bookmark"></i> My Borrowed Books</h1>
    </div>

    <?php if(!empty($borrowed_books)): ?>
        <!-- Search and Sort Controls -->
        <div class="search-sort-controls mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="searchInput" class="form-label"><i class="bi bi-search"></i> Search Books</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title or author..." autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label for="sortSelect" class="form-label"><i class="bi bi-arrow-down-up"></i> Sort By</label>
                    <select class="form-select" id="sortSelect">
                        <option value="title-asc">Title (A-Z)</option>
                        <option value="title-desc">Title (Z-A)</option>
                        <option value="author-asc">Author (A-Z)</option>
                        <option value="author-desc">Author (Z-A)</option>
                        <option value="borrow-newest">Borrow Date (Newest)</option>
                        <option value="borrow-oldest">Borrow Date (Oldest)</option>
                        <option value="due-nearest">Due Date (Nearest)</option>
                        <option value="due-farthest">Due Date (Farthest)</option>
                        <option value="status">Status (Active First)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters" title="Clear all filters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
            <div class="results-info mt-2">
                <small class="text-muted">Showing <span id="resultCount"><?= count($borrowed_books) ?></span> of <?= count($borrowed_books) ?> books</small>
            </div>
        </div>
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
                <tbody id="booksTable">
                    <?php foreach($borrowed_books as $borrow): ?>
                    <?php 
                        $is_overdue = (strtotime($borrow['due_date']) < time() && $borrow['status'] !== 'returned');
                        $days_overdue = $is_overdue ? floor((time() - strtotime($borrow['due_date'])) / 86400) : 0;
                        $borrow_timestamp = strtotime($borrow['borrow_date']);
                        $due_timestamp = strtotime($borrow['due_date']);
                        $status_value = $borrow['status'] === 'returned' ? 'returned' : ($is_overdue ? 'overdue' : 'borrowed');
                    ?>
                    <tr class="book-row" 
                        data-id="<?= $borrow['id'] ?>"
                        data-title="<?= htmlspecialchars($borrow['book_title']) ?>"
                        data-author="<?= htmlspecialchars($borrow['author']) ?>"
                        data-book-id="<?= $borrow['book_id'] ?>"
                        data-borrow-date="<?= $borrow['borrow_date'] ?>"
                        data-borrow-timestamp="<?= $borrow_timestamp ?>"
                        data-due-date="<?= $borrow['due_date'] ?>"
                        data-due-timestamp="<?= $due_timestamp ?>"
                        data-status="<?= $borrow['status'] ?>"
                        data-status-value="<?= $status_value ?>"
                        data-overdue="<?= $is_overdue ? 1 : 0 ?>"
                        data-days-overdue="<?= $days_overdue ?>"
                        <?= $is_overdue ? 'style="background-color: #f8d7da;"' : '' ?>>
                        <td><strong><?= $borrow['book_title'] ?></strong></td>
                        <td><?= $borrow['author'] ?></td>
                        <td><?= date('M d, Y', $borrow_timestamp) ?></td>
                        <td><?= date('M d, Y', $due_timestamp) ?></td>
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
                                <button class="btn btn-sm btn-success" title="Return Book" onclick="returnBook(<?= $borrow['id'] ?>)">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> You haven't borrowed any books yet. <a href="<?= site_url('library/browse') ?>">Browse available books</a> to get started.
        </div>
    <?php endif; ?>
</div>

<script>
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

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const clearButton = document.getElementById('clearFilters');
    const booksTable = document.getElementById('booksTable');
    const bookRows = document.querySelectorAll('.book-row');
    const resultCount = document.getElementById('resultCount');

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const sortBy = sortSelect.value;
        
        // Create array of book rows
        let items = Array.from(bookRows);
        
        // Filter based on search term
        items = items.filter(item => {
            const title = item.getAttribute('data-title').toLowerCase();
            const author = item.getAttribute('data-author').toLowerCase();
            
            return title.includes(searchTerm) || author.includes(searchTerm);
        });

        // Sort based on selected option
        items.sort((a, b) => {
            switch(sortBy) {
                case 'title-asc':
                    return a.getAttribute('data-title').localeCompare(b.getAttribute('data-title'));
                case 'title-desc':
                    return b.getAttribute('data-title').localeCompare(a.getAttribute('data-title'));
                case 'author-asc':
                    return a.getAttribute('data-author').localeCompare(b.getAttribute('data-author'));
                case 'author-desc':
                    return b.getAttribute('data-author').localeCompare(a.getAttribute('data-author'));
                case 'borrow-newest':
                    return parseInt(b.getAttribute('data-borrow-timestamp')) - parseInt(a.getAttribute('data-borrow-timestamp'));
                case 'borrow-oldest':
                    return parseInt(a.getAttribute('data-borrow-timestamp')) - parseInt(b.getAttribute('data-borrow-timestamp'));
                case 'due-nearest':
                    return parseInt(a.getAttribute('data-due-timestamp')) - parseInt(b.getAttribute('data-due-timestamp'));
                case 'due-farthest':
                    return parseInt(b.getAttribute('data-due-timestamp')) - parseInt(a.getAttribute('data-due-timestamp'));
                case 'status':
                    const statusOrder = { 'borrowed': 1, 'overdue': 0, 'returned': 2 };
                    const statusA = statusOrder[a.getAttribute('data-status-value')] || 3;
                    const statusB = statusOrder[b.getAttribute('data-status-value')] || 3;
                    return statusA - statusB;
                default:
                    return 0;
            }
        });

        // Clear and re-add sorted items
        booksTable.innerHTML = '';
        
        if (items.length === 0) {
            booksTable.innerHTML = '<tr><td colspan="6"><div class="alert alert-info no-results"><i class="bi bi-search"></i> No books found matching your search.</div></td></tr>';
        } else {
            items.forEach(item => {
                booksTable.appendChild(item.cloneNode(true));
            });
        }
        
        // Update result count
        resultCount.textContent = items.length;
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterAndSort);
        searchInput.addEventListener('keyup', filterAndSort);
    }
    
    if (sortSelect) {
        sortSelect.addEventListener('change', filterAndSort);
    }
    
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            sortSelect.value = 'title-asc';
            filterAndSort();
        });
    }
});
</script>
