<div class="circulation-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-archive"></i> Archived Circulation Records</h1>
            <a href="<?= site_url('library/circulation') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Active
            </a>
        </div>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <script>
            iziToast.success({
                title: 'Success',
                message: '<?= $this->session->flashdata('success') ?>',
                position: 'topRight'
            });
        </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <script>
            iziToast.error({
                title: 'Oops!',
                message: '<?= $this->session->flashdata('error') ?>',
                position: 'topRight'
            });
        </script>
    <?php endif; ?>

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
                <thead style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
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
                            <a href="<?= site_url('library/circulation/restore/' . $circ['id']) ?>" class="btn btn-sm btn-success" title="Restore" onclick="return confirm('Are you sure you want to restore this record?')">
                                <i class="bi bi-arrow-counterclockwise"></i>
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
            <i class="bi bi-info-circle"></i> No archived circulation records found.
        </div>
    <?php endif; ?>
</div>

<style>
    .circulation-container {
        animation: fadeIn 0.5s ease-in;
    }

    .page-header h1 {
        color: #2c3e50;
        font-weight: 700;
    }

    .header-line {
        border: 3px solid #6c757d;
        margin-bottom: 30px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.5em 0.85em;
        font-weight: 600;
        border-radius: 6px;
    }

    .search-sort-controls .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .results-info {
        color: #6c757d;
    }

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #6c757d;
        border-color: #ddd;
    }

    .pagination .page-link:hover {
        color: #495057;
        background-color: #f0f0f0;
        border-color: #ddd;
    }

    .pagination .page-item.active .page-link {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.5;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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
        const filteredRows = getFilteredRows();
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
        
        // Hide all rows first
        circulationRows.forEach(row => row.style.display = 'none');
        
        // Calculate start and end indices
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        // Show only the rows for the current page
        filteredRows.slice(startIndex, endIndex).forEach(row => {
            row.style.display = '';
        });
        
        // Update result count
        resultCount.textContent = filteredRows.length;
        
        // Update page number display
        if (totalPages > 0) {
            pageNumberDisplay.textContent = currentPage;
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
            noResultsRow.innerHTML = '<td colspan="8"><div class="alert alert-info no-results"><i class="bi bi-search"></i> No archived circulation records found matching your filters.</div></td>';
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
            const filteredRows = getFilteredRows();
            const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    // Initial pagination
    updatePagination();
});
</script>
