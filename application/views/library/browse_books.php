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
                <div class="col-md-4">
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
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters" title="Clear all filters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
            <div class="results-info mt-2">
                <small class="text-muted">Showing <span id="resultCount"><?= count($books) ?></span> of <?= count($books) ?> books</small>
            </div>
        </div>

        <div class="row" id="booksContainer">
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
                        <a href="<?= site_url('library/browse/view/' . $book['id']) ?>" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <?php if($book['available_quantity'] > 0): ?>
                            <a href="<?= site_url('library/borrow/' . $book['id']) ?>" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to borrow this book?')">
                                <i class="bi bi-bookmark-plus"></i> Borrow
                            </a>
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

<style>
    .browse-books-container {
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

    .book-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .book-card .card-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 15px;
        min-height: 50px;
    }

    .book-card .card-text {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .availability-info {
        margin-top: 15px;
    }

    .card-footer {
        padding: 15px;
    }

    .btn-sm {
        margin-right: 5px;
    }

    .search-sort-controls {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .results-info {
        text-align: right;
    }

    .no-results {
        text-align: center;
        padding: 40px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const clearButton = document.getElementById('clearFilters');
    const booksContainer = document.getElementById('booksContainer');
    const bookItems = document.querySelectorAll('.book-item');
    const resultCount = document.getElementById('resultCount');

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

        // Clear and re-add sorted items
        booksContainer.innerHTML = '';
        
        if (items.length === 0) {
            booksContainer.innerHTML = '<div class="col-12"><div class="alert alert-info no-results"><i class="bi bi-search"></i> No books found matching your search.</div></div>';
        } else {
            items.forEach(item => {
                booksContainer.appendChild(item.cloneNode(true));
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
