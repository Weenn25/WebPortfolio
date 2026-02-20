<div class="browse-books-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-search"></i> Browse Books</h1>
    </div>

    <?php if(!empty($books)): ?>
        <div class="row">
            <?php foreach($books as $book): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card book-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $book['title'] ?></h5>
                        <p class="card-text">
                            <strong>Author:</strong> <?= $book['author'] ?><br>
                            <?php if(!empty($book['publisher'])): ?>
                            <strong>Publisher:</strong> <?= $book['publisher'] ?><br>
                            <?php endif; ?>
                            <?php if(!empty($book['isbn'])): ?>
                            <strong>ISBN:</strong> <?= $book['isbn'] ?><br>
                            <?php endif; ?>
                            <?php if(!empty($book['publication_year'])): ?>
                            <strong>Year:</strong> <?= $book['publication_year'] ?><br>
                            <?php endif; ?>
                        </p>
                        
                        <?php if(!empty($book['description'])): ?>
                        <p class="book-description"><?= substr($book['description'], 0, 150) . (strlen($book['description']) > 150 ? '...' : '') ?></p>
                        <?php endif; ?>
                        
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

    .book-description {
        color: #95a5a6;
        font-size: 0.85rem;
        font-style: italic;
        margin-top: 10px;
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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
