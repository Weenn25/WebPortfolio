<div class="circulation-container">
    <div class="page-header mb-4">
        <h1><i class="bi bi-arrow-left-right"></i> Circulation Management</h1>
    </div>

    <?php if(!empty($circulations)): ?>
        <!-- Search and Sort Controls -->
        <div class="search-sort-controls mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="searchInput" class="form-label"><i class="bi bi-search"></i> Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by book title or member name..." autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label"><i class="bi bi-funnel"></i> Filter by Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="overdue">Overdue</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="<?= site_url('library/circulation/archived') ?>" class="btn btn-secondary w-100" style="padding-top: 0.725rem; padding-bottom: 0.725rem;">
                        <i class="bi bi-archive"></i> View Archived
                    </a>
                </div>
            </div>
            <div class="results-info mt-2">
                <small class="text-muted">Showing <span id="resultCount"><?= count($circulations) ?></span> of <?= count($circulations) ?> records</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Member Name</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="circulationTable">
                    <?php foreach($circulations as $circ): ?>
                    <?php 
                        $status = $circ['status'] ?? 'borrowed';
                        $status_class = '';
                        $status_text = '';
                        if($status == 'returned') {
                            $status_class = 'bg-success';
                            $status_text = 'Returned';
                        } else if($status == 'overdue') {
                            $status_class = 'bg-danger';
                            $status_text = 'Overdue';
                        } else {
                            $status_class = 'bg-warning';
                            $status_text = 'Borrowed';
                        }
                    ?>
                    <tr class="circulation-row" 
                        data-id="<?= $circ['id'] ?>"
                        data-book-title="<?= htmlspecialchars($circ['book_title']) ?>"
                        data-member-name="<?= htmlspecialchars($circ['member_name']) ?>"
                        data-status="<?= $status ?>">
                        <td><?= $circ['id'] ?></td>
                        <td><strong><?= $circ['book_title'] ?></strong></td>
                        <td><?= $circ['member_name'] ?></td>
                        <td><?= date('M d, Y', strtotime($circ['borrow_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($circ['due_date'])) ?></td>
                        <td>
                            <?php if(!empty($circ['return_date'])): ?>
                                <?= date('M d, Y', strtotime($circ['return_date'])) ?>
                            <?php else: ?>
                                <em>Not returned</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                        </td>
                        <td>
                            <?php if(empty($circ['return_date'])): ?>
                            <button type="button" class="btn btn-sm btn-success mark-returned-btn" data-id="<?= $circ['id'] ?>" title="Mark as Returned">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-danger archive-btn" data-id="<?= $circ['id'] ?>" title="Archive">
                                <i class="bi bi-archive"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls mt-4">
            <nav aria-label="Table pagination">
                <ul class="pagination justify-content-center" id="paginationList">
                    <!-- Page numbers will be generated by JavaScript -->
                </ul>
            </nav>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No circulation records found. <a href="<?= site_url('library/circulation/new') ?>">Create a new transaction</a> to get started.
        </div>
    <?php endif; ?>
</div>

<style>
    /* Pagination Styling */
    .pagination-controls {
        margin-top: 2rem;
    }

    .pagination .page-item .page-link {
        color: #2c3e50;
        border: 2px solid #3498db;
        background-color: #ffffff;
        font-weight: 500;
        min-width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        margin: 0 4px;
        border-radius: 5px;
    }

    .pagination .page-item .page-link:hover {
        background-color: #3498db;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
    }

    .pagination .page-item.active .page-link {
        background-color: #3498db;
        color: #ffffff;
        border-color: #3498db;
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.4);
    }

    .pagination .page-item.disabled .page-link {
        color: #95a5a6;
        border-color: #ecf0f1;
        background-color: #ecf0f1;
        cursor: not-allowed;
    }

    .pagination .page-item.disabled .page-link:hover {
        background-color: #ecf0f1;
        color: #95a5a6;
        transform: none;
        box-shadow: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const clearButton = document.getElementById('clearFilters');
    const circulationTable = document.getElementById('circulationTable');
    const circulationRows = document.querySelectorAll('.circulation-row');
    const resultCount = document.getElementById('resultCount');
    const paginationList = document.getElementById('paginationList');

    let currentPage = 1;
    let itemsPerPage = 10; // Fixed items per page
    let filteredRows = [];

    function getFilteredRows() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusFilter = filterStatus.value;
        
        return Array.from(circulationRows).filter(row => {
            const bookTitle = row.getAttribute('data-book-title').toLowerCase();
            const memberName = row.getAttribute('data-member-name').toLowerCase();
            const status = row.getAttribute('data-status');
            
            const matchesSearch = !searchTerm || 
                                 bookTitle.includes(searchTerm) || 
                                 memberName.includes(searchTerm);
            
            const matchesStatus = !statusFilter || status === statusFilter;
            
            return matchesSearch && matchesStatus;
        });
    }

    function updatePagination() {
        filteredRows = getFilteredRows();
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
        
        // Ensure current page is within bounds
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = totalPages;
        } else if (currentPage < 1) {
            currentPage = 1;
        }

        // Hide all rows
        circulationRows.forEach(row => row.style.display = 'none');

        // Show rows for current page
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        const rowsToDisplay = filteredRows.slice(startIndex, endIndex);
        rowsToDisplay.forEach(row => row.style.display = '');

        // Update result count
        resultCount.textContent = filteredRows.length;
        
        // Render pagination
        renderPagination(totalPages);

        // Show no results message if needed
        if (filteredRows.length === 0) {
            if (circulationTable.querySelector('.no-results-row')) {
                circulationTable.querySelector('.no-results-row').remove();
            }
            const noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.innerHTML = '<td colspan="8"><div class="alert alert-info no-results"><i class="bi bi-search"></i> No circulation records found matching your filters.</div></td>';
            circulationTable.appendChild(noResultsRow);
        } else {
            const noResultsRow = circulationTable.querySelector('.no-results-row');
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }

    function renderPagination(totalPages) {
        paginationList.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Determine page range to display
        let startPage = 1;
        let endPage = totalPages;
        const maxPagesToShow = 7;

        if (totalPages > maxPagesToShow) {
            const halfWindow = Math.floor(maxPagesToShow / 2);
            startPage = Math.max(1, currentPage - halfWindow);
            endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }
        }

        // Add first page button if needed
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = '<a class="page-link" href="#" data-page="1">1</a>';
            firstLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = 1;
                updatePagination();
            });
            paginationList.appendChild(firstLi);

            if (startPage > 2) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = '<span class="page-link">...</span>';
                paginationList.appendChild(ellipsisLi);
            }
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === currentPage ? ' active' : '');
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            
            li.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = parseInt(this.getAttribute('data-page'));
                updatePagination();
            });
            
            paginationList.appendChild(li);
        }

        // Add last page button if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = '<span class="page-link">...</span>';
                paginationList.appendChild(ellipsisLi);
            }

            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
            lastLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage = totalPages;
                updatePagination();
            });
            paginationList.appendChild(lastLi);
        }
    }

    function filterRecords() {
        currentPage = 1; // Reset to first page on filter change
        updatePagination();
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterRecords);
        searchInput.addEventListener('keyup', filterRecords);
    }
    
    if (filterStatus) {
        filterStatus.addEventListener('change', filterRecords);
    }
    
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            filterStatus.value = '';
            currentPage = 1;
            filterRecords();
        });
    }

    // Initial pagination setup
    updatePagination();

    // Handle Mark as Returned button
    document.querySelectorAll('.mark-returned-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const circulationId = this.getAttribute('data-id');
            iziToast.question({
                timeout: 20000,
                layout: 2,
                title: 'Mark as Returned',
                message: 'Mark this book as returned?',
                position: 'center',
                buttons: [
                    ['<button>OK</button>', function(instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast);
                        window.location.href = '<?= site_url("library/circulation/mark_returned/") ?>' + circulationId;
                    }, true],
                    ['<button>Cancel</button>', function(instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast);
                    }]
                ]
            });
        });
    });

    // Handle Archive button
    document.querySelectorAll('.archive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const circulationId = this.getAttribute('data-id');
            iziToast.question({
                timeout: 20000,
                layout: 2,
                title: 'Archive Record',
                message: 'Are you sure you want to archive this record?',
                position: 'center',
                buttons: [
                    ['<button>OK</button>', function(instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast);
                        window.location.href = '<?= site_url("library/circulation/archive/") ?>' + circulationId;
                    }, true],
                    ['<button>Cancel</button>', function(instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast);
                    }]
                ]
            });
        });
    });
});
</script>
