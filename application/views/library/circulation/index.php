<div class="circulation-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-arrow-left-right"></i> Circulation Management</h1>
            <a href="<?= site_url('library/circulation/archived') ?>" class="btn btn-secondary">
                <i class="bi bi-archive"></i> View Archived
            </a>
        </div>
    </div>

    <?php if(!empty($circulations)): ?>
        <!-- Search and Sort Controls -->
        <div class="search-sort-controls mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="searchInput" class="form-label"><i class="bi bi-search"></i> Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by book title or member name..." autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label for="filterStatus" class="form-label"><i class="bi bi-funnel"></i> Filter by Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="borrowed">Borrowed</option>
                        <option value="overdue">Overdue</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters" title="Clear all filters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
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
                            <a href="<?= site_url('library/circulation/mark_returned/' . $circ['id']) ?>" class="btn btn-sm btn-success" title="Mark as Returned" onclick="return confirm('Mark this book as returned?')">
                                <i class="bi bi-check-circle"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= site_url('library/circulation/archive/' . $circ['id']) ?>" class="btn btn-sm btn-danger" title="Archive" onclick="return confirm('Are you sure you want to archive this record?')">
                                <i class="bi bi-archive"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination-controls mt-4">
            <nav aria-label="Table pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item"><a class="page-link" href="#" id="prevPage">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#" id="pageNumber">1</a></li>
                    <li class="page-item"><a class="page-link" href="#" id="nextPage">Next</a></li>
                </ul>
            </nav>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No circulation records found. <a href="<?= site_url('library/circulation/new') ?>">Create a new transaction</a> to get started.
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const clearButton = document.getElementById('clearFilters');
    const circulationTable = document.getElementById('circulationTable');
    const circulationRows = document.querySelectorAll('.circulation-row');
    const resultCount = document.getElementById('resultCount');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const pageNumberDisplay = document.getElementById('pageNumber');

    let currentPage = 1;
    let itemsPerPage = 10; // Fixed items per page

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

        // Update result count and page display
        resultCount.textContent = filteredRows.length;
        
        // Update pagination display
        if (totalPages > 0) {
            pageNumberDisplay.textContent = currentPage + ' / ' + totalPages;
        } else {
            pageNumberDisplay.textContent = '0';
        }

        // Update button states
        prevPageBtn.parentElement.classList.toggle('disabled', currentPage === 1);
        nextPageBtn.parentElement.classList.toggle('disabled', currentPage === totalPages);

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

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    // Initial pagination setup
    updatePagination();
});
</script>
