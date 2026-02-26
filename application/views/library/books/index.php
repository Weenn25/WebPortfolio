<div class="books-container">
    <div class="page-header mb-4">
        <h1 class="mb-3"><i class="bi bi-book"></i> Books Management</h1>
        
        <!-- Search Bar -->
        <div class="search-bar-inline mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="bookSearchInput" placeholder="Search by title or author..." autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" style="display: none;">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
        
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- View Toggle Dropdown -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">View</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="setViewMode('list'); return false;"><i class="bi bi-list-ul"></i> List View</a></li>
                    <li><a class="dropdown-item" href="#" onclick="setViewMode('card'); return false;"><i class="bi bi-columns-gap"></i> Card View</a></li>
                </ul>
            </div>
            <a href="<?= site_url('library/books/archived') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-archive"></i> <span class="d-none d-md-inline">View Archived</span>
            </a>
            <a href="<?= site_url('library/books/add') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Book</span>
            </a>
        </div>
    </div>



    <?php if(!empty($books)): ?>
        <!-- List View -->
        <div id="listViewContainer" class="books-list-view">
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
                        <tr class="book-item" data-title="<?= strtolower($book['title']) ?>" data-author="<?= strtolower($book['author']) ?>">
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
                                <button class="btn btn-sm btn-info" title="View" onclick="viewBookModal(<?= $book['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" title="Edit" onclick="editBookModal(<?= $book['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Archive" onclick="archiveBook(<?= $book['id'] ?>)">
                                    <i class="bi bi-archive"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card View -->
        <div id="cardViewContainer" class="books-card-view" style="display: none;">
            <div class="row g-4">
                <?php foreach($books as $book): ?>
                <?php 
                    $available = $book['available_quantity'] ?? 0;
                    $status = $available > 0 ? 'Available' : 'Out of Stock';
                    $badge_class = $status === 'Available' ? 'success' : 'danger';
                ?>
                <div class="col-md-6 col-lg-4 book-item" data-title="<?= strtolower($book['title']) ?>" data-author="<?= strtolower($book['author']) ?>">
                    <div class="card book-card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0 text-truncate" title="<?= $book['title'] ?>"><?= $book['title'] ?></h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2"><small><strong>Author:</strong></small></p>
                            <p class="mb-3"><?= $book['author'] ?></p>
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted">Total</small>
                                    <strong><?= $book['total_quantity'] ?? 0 ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted">Available</small>
                                    <strong class="text-<?= $available > 0 ? 'success' : 'danger' ?>"><?= $available ?></strong>
                                </div>
                            </div>
                            
                            <div class="text-center mb-3">
                                <span class="badge bg-<?= $badge_class ?>"><?= $status ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-sm btn-info" title="View" onclick="viewBookModal(<?= $book['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" title="Edit" onclick="editBookModal(<?= $book['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="Archive" onclick="archiveBook(<?= $book['id'] ?>)">
                                    <i class="bi bi-archive"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No books found. <a href="#">Add a new book</a> to get started.
        </div>
    <?php endif; ?>
</div>

<!-- View Book Modal -->
<div class="modal fade" id="viewBookModal" tabindex="-1" aria-labelledby="viewBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="viewBookModalLabel"><i class="bi bi-book"></i> Book Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewBookContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="editBookModalLabel"><i class="bi bi-pencil"></i> Edit Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editBookContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// View Book Modal
function viewBookModal(bookId) {
    const modal = new bootstrap.Modal(document.getElementById('viewBookModal'));
    const contentDiv = document.getElementById('viewBookContent');
    
    contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_book_details/") ?>' + bookId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error('JSON parse error:', text);
                throw new Error('Invalid JSON response');
            }
        })
        .then(data => {
            if (data.success) {
                const book = data.book;
                contentDiv.innerHTML = `
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>${book.id}</td>
                        </tr>
                        <tr>
                            <th>Title:</th>
                            <td><strong>${book.title}</strong></td>
                        </tr>
                        <tr>
                            <th>Author:</th>
                            <td>${book.author}</td>
                        </tr>
                        <tr>
                            <th>Publisher:</th>
                            <td>${book.publisher || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Publication Year:</th>
                            <td>${book.publication_year || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Total Quantity:</th>
                            <td><span class="badge bg-primary">${book.total_quantity}</span></td>
                        </tr>
                        <tr>
                            <th>Available Quantity:</th>
                            <td><span class="badge bg-success">${book.available_quantity}</span></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                ${book.available_quantity > 0 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Out of Stock</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>${new Date(book.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>${book.updated_at ? new Date(book.updated_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</td>
                        </tr>
                    </table>
                    ${book.description ? `<hr><div><h6>Description:</h6><p>${book.description}</p></div>` : ''}
                `;
            } else {
                iziToast.error({
                    title: 'Error',
                    message: data.message || 'Failed to load book details',
                    position: 'topRight'
                });
                modal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            iziToast.error({
                title: 'Error',
                message: 'Failed to load book details',
                position: 'topRight'
            });
            modal.hide();
        });
    
    modal.show();
}

// Edit Book Modal
function editBookModal(bookId) {
    const modal = new bootstrap.Modal(document.getElementById('editBookModal'));
    const contentDiv = document.getElementById('editBookContent');
    
    contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_book_edit/") ?>' + bookId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error('JSON parse error:', text);
                throw new Error('Invalid JSON response');
            }
        })
        .then(data => {
            if (data.success) {
                const book = data.book;
                contentDiv.innerHTML = `
                    <form id="editBookForm" onsubmit="submitEditBook(event, ${bookId})">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_title" name="title" value="${book.title}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_author" class="form-label">Author <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_author" name="author" value="${book.author}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_publisher" class="form-label">Publisher</label>
                                    <input type="text" class="form-control" id="edit_publisher" name="publisher" value="${book.publisher || ''}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_publication_year" class="form-label">Publication Year</label>
                                    <input type="number" class="form-control" id="edit_publication_year" name="publication_year" value="${book.publication_year || ''}" min="1000" max="${new Date().getFullYear()}">
                                </div>

                                <div class="mb-3">
                                    <label for="edit_total_quantity" class="form-label">Total Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_total_quantity" name="total_quantity" value="${book.total_quantity}" required min="0">
                                </div>

                                <div class="mb-3">
                                    <label for="edit_available_quantity" class="form-label">Available Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_available_quantity" name="available_quantity" value="${book.available_quantity}" required min="0">
                                    <small class="text-muted">Must be ≤ total quantity</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3">${book.description || ''}</textarea>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Book
                            </button>
                        </div>
                    </form>
                `;
                
                // Validate available quantity
                const availInput = document.getElementById('edit_available_quantity');
                const totalInput = document.getElementById('edit_total_quantity');
                
                if (availInput && totalInput) {
                    availInput.addEventListener('input', function() {
                        const totalQty = parseInt(totalInput.value) || 0;
                        const availQty = parseInt(this.value) || 0;
                        
                        if (availQty > totalQty) {
                            this.setCustomValidity('Available quantity cannot exceed total quantity');
                        } else {
                            this.setCustomValidity('');
                        }
                    });

                    totalInput.addEventListener('input', function() {
                        availInput.dispatchEvent(new Event('input'));
                    });
                }
            } else {
                iziToast.error({
                    title: 'Error',
                    message: data.message || 'Failed to load book for editing',
                    position: 'topRight'
                });
                modal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            iziToast.error({
                title: 'Error',
                message: 'Failed to load book for editing',
                position: 'topRight'
            });
            modal.hide();
        });
    
    modal.show();
}

// Submit Edit Book Form
function submitEditBook(event, bookId) {
    event.preventDefault();
    
    const form = document.getElementById('editBookForm');
    const formData = new FormData(form);
    
    // Add tab_id to FormData
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    if (tabId) {
        formData.append('tab_id', tabId);
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
    
    fetch('<?= site_url("library/update_book/") ?>' + bookId, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
    })
    .then(text => {
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error('JSON parse error:', text);
            throw new Error('Invalid JSON response');
        }
    })
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Update Book';
        
        if (data.success) {
            iziToast.success({
                title: 'Success',
                message: data.message || 'Book updated successfully',
                position: 'topRight'
            });
            
            // Reload page after 1.5 seconds
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            iziToast.error({
                title: 'Error',
                message: data.message || 'Failed to update book',
                position: 'topRight'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Update Book';
        
        iziToast.error({
            title: 'Error',
            message: 'An error occurred while updating the book',
            position: 'topRight'
        });
    });
}

function archiveBook(bookId) {
    iziToast.show({
        timeout: 20000,
        layout: 2,
        title: '<i class="bi bi-archive"></i> Archive Book',
        message: 'Are you sure you want to archive this book?',
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
            ['<button class="btn btn-light btn-sm" style="font-weight: 600; padding: 10px 24px; border: none; cursor: pointer; touch-action: auto;"><i class="bi bi-check-circle"></i> YES, ARCHIVE</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
                window.location.href = '<?= site_url("library/books/archive/") ?>' + bookId;
            }, true],
            ['<button class="btn btn-outline-light btn-sm" style="font-weight: 600; padding: 10px 24px; border-width: 2px; cursor: pointer; touch-action: auto;"><i class="bi bi-x-circle"></i> CANCEL</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
            }]
        ]
    });
}

// Toggle between list and card view
function setViewMode(mode) {
    const listView = document.getElementById('listViewContainer');
    const cardView = document.getElementById('cardViewContainer');
    
    // Save preference to localStorage
    localStorage.setItem('booksViewMode', mode);
    
    if (mode === 'list') {
        listView.style.display = 'block';
        cardView.style.display = 'none';
    } else {
        listView.style.display = 'none';
        cardView.style.display = 'block';
    }
}

// Initialize view on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('booksViewMode') || 'list';
    setViewMode(savedMode);
});

// Real-time search functionality
const searchInput = document.getElementById('bookSearchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');

searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    
    // Show/hide clear button
    if (searchTerm.length > 0) {
        clearSearchBtn.style.display = 'inline-block';
    } else {
        clearSearchBtn.style.display = 'none';
    }
    
    // Filter books
    const bookItems = document.querySelectorAll('.book-item');
    
    bookItems.forEach(item => {
        const title = item.dataset.title || '';
        const author = item.dataset.author || '';
        
        if (title.includes(searchTerm) || author.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Clear search button
clearSearchBtn.addEventListener('click', function() {
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
    searchInput.focus();
});
</script>
