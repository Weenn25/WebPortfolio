<div class="circulation-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-arrow-left-right"></i> Circulation Management</h1>
            <button class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Transaction
            </button>
        </div>
    </div>

    <?php if(!empty($circulations)): ?>
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
                <tbody>
                    <?php foreach($circulations as $circ): ?>
                    <tr>
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
                            <?php 
                                $status = $circ['status'] ?? 'borrowed';
                                if($status == 'returned') {
                                    echo '<span class="badge bg-success">Returned</span>';
                                } else if($status == 'overdue') {
                                    echo '<span class="badge bg-danger">Overdue</span>';
                                } else {
                                    echo '<span class="badge bg-warning">Borrowed</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if(empty($circ['return_date'])): ?>
                            <button class="btn btn-sm btn-success" title="Mark as Returned">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No circulation records found. <a href="#">Create a new transaction</a> to get started.
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
        border: 3px solid #3498db;
        margin-bottom: 30px;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        font-weight: 600;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-sm {
        margin: 0 2px;
    }
</style>
