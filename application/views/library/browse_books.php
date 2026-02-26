<div class="browse-books-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-search"></i> Browse Books</h1>
    </div>

    <?php if(!empty($books)): ?>
        <!-- Search and Sort Controls -->
        <div class="search-sort-controls mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="searchInput" class="form-label"><i class="bi bi-search"></i> Search Books</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title, author, publisher..." autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="sortSelect" class="form-label"><i class="bi bi-arrow-down-up"></i> Sort By</label>
                    <select class="form-select" id="sortSelect">
                        <option value="title-asc">Title (A-Z)</option>
                        <option value="title-desc">Title (Z-A)</option>
                        <option value="author-asc">Author (A-Z)</option>
                        <option value="author-desc">Author (Z-A)</option>
                        <option value="year-newest">Year (Newest)</option>
                        <option value="year-oldest">Year (Oldest)</option>
                        <option value="available">Availability (Available First)</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <div class="dropdown flex-grow-1">
                        <button class="btn btn-outline-secondary w-100 dropdown-toggle" type="button" id="viewDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-eye"></i> View By
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="viewDropdown">
                            <li>
                                <input type="radio" class="btn-check" name="viewMode" id="cardView" value="card" checked>
                                <label class="dropdown-item" for="cardView" style="cursor: pointer;">
                                    <i class="bi bi-grid-3x2-gap"></i> Card View
                                </label>
                            </li>
                            <li>
                                <input type="radio" class="btn-check" name="viewMode" id="listView" value="list">
                                <label class="dropdown-item" for="listView" style="cursor: pointer;">
                                    <i class="bi bi-list-ul"></i> List View
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="results-info mt-2">
                <small class="text-muted">Showing <span id="resultCount"><?= count($books) ?></span> of <?= count($books) ?> books</small>
            </div>
        </div>

        <div class="row" id="booksContainer" data-view-mode="card">
            <?php foreach($books as $book): ?>
            <div class="col-md-6 col-lg-4 mb-4 book-item" data-title="<?= htmlspecialchars($book['title']) ?>" data-author="<?= htmlspecialchars($book['author']) ?>" data-publisher="<?= htmlspecialchars($book['publisher'] ?? '') ?>" data-description="<?= htmlspecialchars($book['description'] ?? '') ?>" data-year="<?= $book['publication_year'] ?? '' ?>" data-available="<?= $book['available_quantity'] ?>">
                <div class="card book-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $book['title'] ?></h5>
                        <p class="card-text">
                            <strong>Author:</strong> <?= $book['author'] ?><br>
                            <?php if(!empty($book['publisher'])): ?>
                            <strong>Publisher:</strong> <?= $book['publisher'] ?><br>
                            <?php endif; ?>
                            <?php if(!empty($book['publication_year'])): ?>
                            <strong>Year:</strong> <?= $book['publication_year'] ?><br>
                            <?php endif; ?>
                        </p>
                        
                        <div class="availability-info">
                            <?php if($book['available_quantity'] > 0): ?>
                                <span class="badge bg-success">Available: <?= $book['available_quantity'] ?> / <?= $book['total_quantity'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bookDetailsModal" onclick="showBookDetails(<?= $book['id'] ?>)">
                            <i class="bi bi-eye"></i> View Details
                        </button>
                        <?php if($book['available_quantity'] > 0): ?>
                            <button class="btn btn-primary btn-sm" onclick="borrowBook(<?= $book['id'] ?>)">
                                <i class="bi bi-bookmark-plus"></i> Borrow
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="bi bi-bookmark-x"></i> Unavailable
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No books available at the moment. Please check back later.
        </div>
    <?php endif; ?>
</div>

<!-- Book Details Modal -->
<div class="modal fade" id="bookDetailsModal" tabindex="-1" aria-labelledby="bookDetailsModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" tabindex="-1">
            <div class="modal-header">
                <h5 class="modal-title" id="bookDetailsModalLabel">Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBookContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Borrow Duration Modal -->
<div class="modal fade" id="borrowDurationModal" tabindex="-1" aria-labelledby="borrowDurationModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="borrowDurationModalLabel"><i class="bi bi-bookmark-plus"></i> Borrow Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="borrowBookId" value="">
                <p class="mb-3">How many days would you like to borrow this book?</p>
                <div class="mb-3">
                    <label for="borrowDays" class="form-label fw-bold">Number of Days</label>
                    <input type="number" class="form-control form-control-lg" id="borrowDays" min="1" max="14" value="14" placeholder="Enter number of days" inputmode="numeric">
                </div>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Due Date:</strong> <span id="calculatedDueDate"></span><br>
                        <small class="text-muted">Books returned after the due date will be marked as overdue.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBorrowBtn">
                    <i class="bi bi-check-circle"></i> Confirm Borrow
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showBookDetails(bookId) {
    const modalContent = document.getElementById('modalBookContent');
    const modal = document.getElementById('bookDetailsModal');
    
    // Fetch book details via AJAX
    fetch('<?= site_url("library/get_book_details/") ?>' + bookId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const book = data.book;
                let html = `
                    <div class="book-details-content">
                        <h4>${book.title}</h4>
                        <hr>
                        <p><strong>Author:</strong> ${book.author}</p>
                        ${book.publisher ? `<p><strong>Publisher:</strong> ${book.publisher}</p>` : ''}
                        ${book.publication_year ? `<p><strong>Publication Year:</strong> ${book.publication_year}</p>` : ''}
                        <p><strong>Total Quantity:</strong> ${book.total_quantity}</p>
                        <p><strong>Available Quantity:</strong> <span class="badge ${book.available_quantity > 0 ? 'bg-success' : 'bg-danger'}">${book.available_quantity}</span></p>
                        ${book.description ? `<hr><p><strong>Description:</strong></p><p>${book.description}</p>` : ''}
                    </div>
                `;
                modalContent.innerHTML = html;
                // Ensure modal is shown
                if (modal && window.bootstrap) {
                    let bsModal = new window.bootstrap.Modal(modal);
                    bsModal.show();
                }
            } else {
                modalContent.innerHTML = '<div class="alert alert-danger">Error loading book details</div>';
            }
        })
        .catch(error => {
            modalContent.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });
}

// Add global cleanup for modal backdrops
if (document.getElementById('bookDetailsModal')) {
    document.getElementById('bookDetailsModal').addEventListener('hidden.bs.modal', function() {
        // Remove all modal backdrops
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.remove();
        });
        // Reset body
        document.body.style.overflow = '';
        document.body.classList.remove('modal-open');
    });
}

function borrowBook(bookId) {
    // Set the book ID in the modal
    document.getElementById('borrowBookId').value = bookId;
    
    // Reset to default 14 days
    document.getElementById('borrowDays').value = 14;
    
    // Calculate and display the initial due date
    updateDueDate();
    
    // Show the borrow duration modal
    const modal = new bootstrap.Modal(document.getElementById('borrowDurationModal'));
    modal.show();
}

function updateDueDate() {
    let daysValue = document.getElementById('borrowDays').value;
    
    // Allow empty field (for user to clear and retype)
    if (daysValue === '' || daysValue === null) {
        document.getElementById('calculatedDueDate').textContent = 'Please enter a number';
        return;
    }
    
    let days = parseInt(daysValue);
    
    // Validate and constrain days (1-14)
    if (isNaN(days) || days < 1) {
        days = 1;
    } else if (days > 14) {
        days = 14;
        document.getElementById('borrowDays').value = 14;
    }
    
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + days);
    
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('calculatedDueDate').textContent = dueDate.toLocaleDateString('en-US', options);
}

// Update due date when input changes
document.addEventListener('DOMContentLoaded', function() {
    const borrowDaysInput = document.getElementById('borrowDays');
    if (borrowDaysInput) {
        // Update on input change (allows deletion)
        borrowDaysInput.addEventListener('input', function() {
            // Allow numbers and empty value
            if (this.value === '' || /^\d+$/.test(this.value)) {
                updateDueDate();
            } else {
                // If non-numeric, clear it
                this.value = '';
                document.getElementById('calculatedDueDate').textContent = 'Please enter a number';
            }
        });
    }
    
    // Handle confirm borrow button
    const confirmBorrowBtn = document.getElementById('confirmBorrowBtn');
    if (confirmBorrowBtn) {
        confirmBorrowBtn.addEventListener('click', function() {
            const bookId = document.getElementById('borrowBookId').value;
            let daysValue = document.getElementById('borrowDays').value;
            
            // Check if empty
            if (daysValue === '' || daysValue === null) {
                alert('Please enter the number of days');
                return;
            }
            
            let days = parseInt(daysValue);
            
            // Final validation
            if (isNaN(days) || days < 1) {
                days = 1;
            } else if (days > 14) {
                days = 14;
            }
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('borrowDurationModal'));
            modal.hide();
            
            // Redirect to borrow with days parameter
            window.location.href = '<?= site_url("library/borrow/") ?>' + bookId + '/' + days;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortSelect');
    const booksContainer = document.getElementById('booksContainer');
    const bookItems = document.querySelectorAll('.book-item');
    const resultCount = document.getElementById('resultCount');
    const viewModeButtons = document.querySelectorAll('input[name="viewMode"]');

    function renderBooks(items) {
        const viewMode = booksContainer.getAttribute('data-view-mode');
        booksContainer.innerHTML = '';
        
        if (items.length === 0) {
            booksContainer.innerHTML = '<div class="col-12"><div class="alert alert-info no-results"><i class="bi bi-search"></i> No books found matching your search.</div></div>';
            return;
        }

        if (viewMode === 'list') {
            // List view
            const listHTML = items.map(item => {
                const bookCard = item.querySelector('.book-card');
                const title = item.getAttribute('data-title');
                const author = item.getAttribute('data-author');
                const publisher = item.getAttribute('data-publisher');
                const year = item.getAttribute('data-year');
                const available = item.getAttribute('data-available');
                const total = bookCard.querySelector('.badge').textContent.match(/\d+\s*\/\s*(\d+)/)?.[1] || '0';
                const bookId = item.querySelector('.btn-info').onclick.toString().match(/\d+/)[0];

                return `
                    <div class="col-12 mb-3 book-item-list" data-title="${title}" data-author="${author}" data-publisher="${publisher}" data-year="${year}" data-available="${available}">
                        <div class="card book-list-card h-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="card-title">${title}</h6>
                                        <p class="card-text mb-2">
                                            <small><strong>Author:</strong> ${author}</small><br>
                                            ${publisher ? `<small><strong>Publisher:</strong> ${publisher}</small><br>` : ''}
                                            ${year ? `<small><strong>Year:</strong> ${year}</small>` : ''}
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="card-text mb-0">
                                            <small><strong>Quantity:</strong></small><br>
                                            <small>Available: <span class="badge ${available > 0 ? 'bg-success' : 'bg-danger'}">${available} / ${total}</span></small>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-md-end text-start mt-2 mt-md-0">
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bookDetailsModal" onclick="showBookDetails(${bookId})">
                                            <i class="bi bi-eye"></i> Details
                                        </button>
                                        ${available > 0 ? 
                                            `<button class="btn btn-primary btn-sm" onclick="borrowBook(${bookId})">
                                                <i class="bi bi-bookmark-plus"></i> Borrow
                                            </button>` : 
                                            `<button class="btn btn-secondary btn-sm" disabled>
                                                <i class="bi bi-bookmark-x"></i> Out
                                            </button>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            booksContainer.innerHTML = listHTML;
        } else {
            // Card view
            items.forEach(item => {
                booksContainer.appendChild(item.cloneNode(true));
            });
        }
    }

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const sortBy = sortSelect.value;
        
        // Create array of book items
        let items = Array.from(bookItems);
        
        // Filter based on search term
        items = items.filter(item => {
            const title = item.getAttribute('data-title').toLowerCase();
            const author = item.getAttribute('data-author').toLowerCase();
            const publisher = item.getAttribute('data-publisher').toLowerCase();
            const description = item.getAttribute('data-description').toLowerCase();
            const year = item.getAttribute('data-year').toLowerCase();
            
            return title.includes(searchTerm) || 
                   author.includes(searchTerm) ||
                   publisher.includes(searchTerm) ||
                   description.includes(searchTerm) ||
                   year.includes(searchTerm);
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
                case 'year-newest':
                    return parseInt(b.getAttribute('data-year') || 0) - parseInt(a.getAttribute('data-year') || 0);
                case 'year-oldest':
                    return parseInt(a.getAttribute('data-year') || 0) - parseInt(b.getAttribute('data-year') || 0);
                case 'available':
                    return parseInt(b.getAttribute('data-available') || 0) - parseInt(a.getAttribute('data-available') || 0);
                default:
                    return 0;
            }
        });

        renderBooks(items);
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

    // View mode toggle
    viewModeButtons.forEach(button => {
        button.addEventListener('change', function() {
            booksContainer.setAttribute('data-view-mode', this.value);
            booksContainer.classList.toggle('list-view', this.value === 'list');
            booksContainer.classList.toggle('card-view', this.value === 'card');
            filterAndSort();
        });
    });
});
</script>
