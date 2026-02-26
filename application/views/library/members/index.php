<div class="members-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="bi bi-people"></i> Members Management</h1>
            <div>
                <button type="button" class="btn btn-secondary" onclick="viewInactiveMembers()">
                    <i class="bi bi-person-x"></i> View Inactive Members
                </button>
                <button type="button" class="btn btn-primary" onclick="addNewMemberModal()">
                    <i class="bi bi-plus-circle"></i> Add New Member
                </button>
            </div>
        </div>
    </div>



    <?php if(!empty($members)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Member Since</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $member): ?>
                    <tr data-member-id="<?= $member['id'] ?>">
                        <td><?= $member['id'] ?></td>
                        <td><strong><?= $member['first_name'] . ' ' . $member['last_name'] ?></strong></td>
                        <td><?= $member['email'] ?></td>
                        <td><?= isset($member['created_at']) ? date('M d, Y', strtotime($member['created_at'])) : 'N/A' ?></td>
                        <td>
                            <?php if($member['is_active']): ?>
                                <span class="badge bg-success">ACTIVE</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">INACTIVE</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" title="View" onclick="viewMemberModal(<?= $member['id'] ?>)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" title="Edit" onclick="editMemberModal(<?= $member['id'] ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Deactivate" onclick="deactivateMember(<?= $member['id'] ?>)">
                                <i class="bi bi-lock"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> No members found. <a href="#">Add a new member</a> to get started.
        </div>
    <?php endif; ?>
</div>

<!-- View Member Modal -->
<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="viewMemberLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="viewMemberLabel"><i class="bi bi-person"></i> Member Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewMemberContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="editMemberLabel"><i class="bi bi-pencil-square"></i> Edit Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="editMemberContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="addMemberLabel"><i class="bi bi-person-plus"></i> Add New Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="addMemberContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inactive Members Modal -->
<div class="modal fade" id="inactiveMembersModal" tabindex="-1" aria-labelledby="inactiveMembersLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;">
                <h5 class="modal-title" id="inactiveMembersLabel"><i class="bi bi-person-x"></i> Inactive Members</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="inactiveMembersContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Member Modal (from inactive list) -->
<div class="modal fade" id="inactiveMemberDetailModal" tabindex="-1" aria-labelledby="inactiveMemberDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                <h5 class="modal-title" id="inactiveMemberDetailLabel"><i class="bi bi-person"></i> Member Details</h5>
                <button type="button" class="btn-close btn-close-white" onclick="backToInactiveList()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="inactiveMemberDetailContent">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="backToInactiveList()">
                    <i class="bi bi-arrow-left"></i> Back to List
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-dialog {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform, opacity;
    visibility: visible;
}

.modal-dialog.modal-slide-out {
    transform: translateX(-100%);
    opacity: 0;
}

.modal-dialog.modal-slide-in {
    transform: translateX(0);
    opacity: 1;
}

/* Prevent Bootstrap fade transitions during custom slide animations */
#inactiveMembersModal.fade .modal-dialog,
#inactiveMemberDetailModal.fade .modal-dialog {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Keep modals displayed during transitions */
#inactiveMembersModal,
#inactiveMemberDetailModal {
    transition: visibility 0s linear;
}

#inactiveMembersModal.show,
#inactiveMemberDetailModal.show {
    visibility: visible;
}

/* Smooth backdrop */
.modal-backdrop {
    opacity: 0;
    transition: opacity 0.2s linear;
}

.modal-backdrop.show {
    opacity: 0.5;
}
</style>

<script>
let currentEditMemberId = null;

// View Member Modal
function viewMemberModal(memberId) {
    const modal = new bootstrap.Modal(document.getElementById('viewMemberModal'));
    const contentDiv = document.getElementById('viewMemberContent');
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_member_details/") ?>' + memberId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
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
            if (data.success && data.member) {
                const member = data.member;
                contentDiv.innerHTML = `
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%; background-color: #f8f9fa;">ID:</th>
                            <td>${member.id}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Name:</th>
                            <td>${member.first_name} ${member.last_name}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Email:</th>
                            <td>${member.email}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Membership Date:</th>
                            <td>${new Date(member.membership_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Status:</th>
                            <td>
                                ${member.is_active ? '<span class="badge bg-success">ACTIVE</span>' : '<span class="badge bg-secondary">INACTIVE</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Created At:</th>
                            <td>${new Date(member.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        </tr>
                    </table>
                `;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member details</div>';
                iziToast.error({title: 'Error', message: data.message || 'Failed to load member details'});
            }
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member details</div>';
            iziToast.error({title: 'Error', message: 'Failed to load member details'});
            modal.show();
        });
}

// Edit Member Modal
function editMemberModal(memberId) {
    const modal = new bootstrap.Modal(document.getElementById('editMemberModal'));
    const contentDiv = document.getElementById('editMemberContent');
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_member_edit/") ?>' + memberId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
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
            if (data.success && data.member) {
                const member = data.member;
                currentEditMemberId = memberId;
                contentDiv.innerHTML = `
                    <form onsubmit="submitEditMember(event, ${memberId})">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="${member.first_name}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="${member.last_name}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="${member.email}" required>
                        </div>
                        <div class="mb-3">
                            <label for="membership_date" class="form-label">Membership Date:</label>
                            <input type="date" class="form-control" id="membership_date" name="membership_date" value="${member.membership_date.split(' ')[0]}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <span id="updateBtnText">Update Member</span>
                                <span id="updateBtnSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                            </button>
                        </div>
                    </form>
                `;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member</div>';
                iziToast.error({title: 'Error', message: data.message || 'Failed to load member'});
            }
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member</div>';
            iziToast.error({title: 'Error', message: 'Failed to load member'});
            modal.show();
        });
}

// Submit Edit Member
function submitEditMember(event, memberId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    if (tabId) {
        formData.append('tab_id', tabId);
    }
    
    const btnText = document.getElementById('updateBtnText');
    const btnSpinner = document.getElementById('updateBtnSpinner');
    btnText.style.display = 'none';
    btnSpinner.style.display = 'inline-block';
    
    fetch('<?= site_url("library/update_member_ajax/") ?>' + memberId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error('JSON parse error:', text);
            throw new Error('Invalid JSON response');
        }
    })
    .then(data => {
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        
        if (data.success) {
            // Update table row with new data
            const firstNameInput = document.getElementById('first_name');
            const lastNameInput = document.getElementById('last_name');
            const emailInput = document.getElementById('email');
            
            const tableRow = document.querySelector(`tr[data-member-id="${memberId}"]`);
            if (tableRow) {
                tableRow.querySelector('td:nth-child(2)').innerHTML = `<strong>${firstNameInput.value} ${lastNameInput.value}</strong>`;
                tableRow.querySelector('td:nth-child(3)').innerHTML = emailInput.value;
            }
            
            iziToast.success({title: 'Success', message: 'Member updated successfully'});
            bootstrap.Modal.getInstance(document.getElementById('editMemberModal')).hide();
        } else {
            iziToast.error({title: 'Error', message: data.message || 'Failed to update member'});
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        iziToast.error({title: 'Error', message: 'Failed to update member'});
    });
}

// Deactivate Member
function deactivateMember(memberId) {
    iziToast.show({
        timeout: 20000,
        layout: 2,
        title: '<i class="bi bi-person-x"></i> Deactivate Member',
        message: 'Are you sure you want to deactivate this member?',
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
            ['<button class="btn btn-light btn-sm" style="font-weight: 600; padding: 10px 24px; border: none; cursor: pointer; touch-action: auto;"><i class="bi bi-check-circle"></i> YES, DEACTIVATE</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
                confirmDeactivate(memberId);
            }, true],
            ['<button class="btn btn-outline-light btn-sm" style="font-weight: 600; padding: 10px 24px; border-width: 2px; cursor: pointer; touch-action: auto;"><i class="bi bi-x-circle"></i> CANCEL</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
            }]
        ]
    });
}

// Confirm Deactivate
function confirmDeactivate(memberId) {
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/deactivate_member_ajax/") ?>' + memberId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
    fetch(url)
        .then(response => response.text())
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
                iziToast.success({title: 'Success', message: 'Member deactivated successfully'});
                setTimeout(() => location.reload(), 1500);
            } else {
                iziToast.error({title: 'Error', message: data.message || 'Failed to deactivate member'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            iziToast.error({title: 'Error', message: 'Failed to deactivate member'});
        });
}

// View Inactive Members
function viewInactiveMembers() {
    const modal = new bootstrap.Modal(document.getElementById('inactiveMembersModal'));
    const contentDiv = document.getElementById('inactiveMembersContent');
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_inactive_members_ajax/") ?>' + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
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
            if (data.success && data.members) {
                const members = data.members;
                let html = '<div class="table-responsive"><table class="table table-hover table-striped"><thead style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white;"><tr><th>ID</th><th>Name</th><th>Email</th><th>Member Since</th><th>Deactivated</th><th>Actions</th></tr></thead><tbody>';
                
                members.forEach(member => {
                    html += `<tr style="background-color: #f8f9fa;">
                        <td>${member.id}</td>
                        <td><strong>${member.first_name} ${member.last_name}</strong></td>
                        <td>${member.email || 'N/A'}</td>
                        <td>${new Date(member.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        <td>${member.updated_at ? new Date(member.updated_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" title="View" onclick="viewInactiveMemberDetail(${member.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" title="Activate" onclick="activateMemberModal(${member.id})">
                                <i class="bi bi-unlock"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                contentDiv.innerHTML = html;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-info">No inactive members found. All members are currently active.</div>';
            }
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading inactive members</div>';
            iziToast.error({title: 'Error', message: 'Failed to load inactive members'});
            modal.show();
        });
}

// Add New Member Modal
function addNewMemberModal() {
    const modal = new bootstrap.Modal(document.getElementById('addMemberModal'));
    const contentDiv = document.getElementById('addMemberContent');
    
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_add_member_form/") ?>' + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
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
                contentDiv.innerHTML = `
                    <form onsubmit="submitAddMember(event)">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_first_name" name="first_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="add_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_last_name" name="last_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="add_email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="add_membership_date" class="form-label">Membership Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="add_membership_date" name="membership_date" required max="${new Date().toISOString().split('T')[0]}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="add_username" name="username" required placeholder="Create login username">
                                    <small class="text-muted">Username must be unique</small>
                                </div>
                                <div class="mb-3">
                                    <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="add_password" name="password" required placeholder="Min. 6 characters">
                                    <small class="text-muted">Password must be at least 6 characters</small>
                                </div>
                                <div class="mb-3">
                                    <label for="add_confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="add_confirm_password" name="confirm_password" required placeholder="Re-enter password">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <span id="addBtnText">Add Member</span>
                                <span id="addBtnSpinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                            </button>
                        </div>
                    </form>
                `;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading form</div>';
            }
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading form</div>';
            iziToast.error({title: 'Error', message: 'Failed to load form'});
            modal.show();
        });
}

// Submit Add Member
function submitAddMember(event) {
    event.preventDefault();
    
    const form = event.target;
    const password = document.getElementById('add_password').value;
    const confirmPassword = document.getElementById('add_confirm_password').value;
    
    // Validate password length
    if (password.length < 6) {
        iziToast.error({title: 'Validation Error', message: 'Password must be at least 6 characters long'});
        return;
    }
    
    // Check if passwords match
    if (password !== confirmPassword) {
        iziToast.error({title: 'Validation Error', message: 'Passwords do not match'});
        return;
    }
    
    const formData = new FormData(form);
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    if (tabId) {
        formData.append('tab_id', tabId);
    }
    
    const btnText = document.getElementById('addBtnText');
    const btnSpinner = document.getElementById('addBtnSpinner');
    btnText.style.display = 'none';
    btnSpinner.style.display = 'inline-block';
    
    fetch('<?= site_url("library/insert_member_ajax/") ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error('JSON parse error:', text);
            throw new Error('Invalid JSON response');
        }
    })
    .then(data => {
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        
        if (data.success) {
            iziToast.success({title: 'Success', message: 'Member added successfully with account credentials'});
            bootstrap.Modal.getInstance(document.getElementById('addMemberModal')).hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            iziToast.error({title: 'Error', message: data.message || 'Failed to add member'});
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        iziToast.error({title: 'Error', message: 'Failed to add member'});
    });
}

// Activate Member Modal
function activateMemberModal(memberId) {
    iziToast.show({
        timeout: 20000,
        layout: 2,
        title: '<i class="bi bi-person-check"></i> Activate Member',
        message: 'Are you sure you want to activate this member?',
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
            ['<button class="btn btn-light btn-sm" style="font-weight: 600; padding: 10px 24px; border: none; cursor: pointer; touch-action: auto;"><i class="bi bi-check-circle"></i> YES, ACTIVATE</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
                confirmActivate(memberId);
            }, true],
            ['<button class="btn btn-outline-light btn-sm" style="font-weight: 600; padding: 10px 24px; border-width: 2px; cursor: pointer; touch-action: auto;"><i class="bi bi-x-circle"></i> CANCEL</button>', function(instance, toast) {
                instance.hide({ transitionOut: 'fadeOut' }, toast);
            }]
        ]
    });
}

// Confirm Activate
function confirmActivate(memberId) {
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/activate_member_ajax/") ?>' + memberId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
    fetch(url)
        .then(response => response.text())
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
                iziToast.success({title: 'Success', message: 'Member activated successfully'});
                setTimeout(() => location.reload(), 1500);
            } else {
                iziToast.error({title: 'Error', message: data.message || 'Failed to activate member'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            iziToast.error({title: 'Error', message: 'Failed to activate member'});
        });
}

// View Inactive Member Detail with seamless slide transition
function viewInactiveMemberDetail(memberId) {
    const inactiveModalElement = document.getElementById('inactiveMembersModal');
    const inactiveModal = bootstrap.Modal.getInstance(inactiveModalElement);
    const inactiveDialog = inactiveModalElement.querySelector('.modal-dialog');
    const detailModalElement = document.getElementById('inactiveMemberDetailModal');
    const detailDialog = detailModalElement.querySelector('.modal-dialog');
    const contentDiv = document.getElementById('inactiveMemberDetailContent');
    
    // Prepare detail modal
    detailDialog.classList.remove('modal-slide-out');
    detailDialog.classList.add('modal-slide-in');
    
    // Show detail modal immediately with loading state
    const detailModal = new bootstrap.Modal(detailModalElement, { backdrop: false });
    detailModal.show();
    
    // Slide out inactive modal simultaneously
    inactiveDialog.classList.add('modal-slide-out');
    
    // Fetch data while transition is happening
    const tabId = sessionStorage.getItem('currentTabId') || sessionStorage.getItem('tabId');
    const url = '<?= site_url("library/get_member_details/") ?>' + memberId + (tabId ? '?tab_id=' + encodeURIComponent(tabId) : '');
    
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
            if (data.success && data.member) {
                const member = data.member;
                contentDiv.innerHTML = `
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%; background-color: #f8f9fa;">ID:</th>
                            <td>${member.id}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Name:</th>
                            <td>${member.first_name} ${member.last_name}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Email:</th>
                            <td>${member.email}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Membership Date:</th>
                            <td>${new Date(member.membership_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Status:</th>
                            <td>
                                ${member.is_active ? '<span class="badge bg-success">ACTIVE</span>' : '<span class="badge bg-secondary">INACTIVE</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th style="background-color: #f8f9fa;">Created At:</th>
                            <td>${new Date(member.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</td>
                        </tr>
                    </table>
                `;
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member details</div>';
                iziToast.error({title: 'Error', message: data.message || 'Failed to load member details'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading member details</div>';
            iziToast.error({title: 'Error', message: 'Failed to load member details'});
        });
    
    // Hide inactive modal after transition completes
    setTimeout(() => {
        if (inactiveModal) {
            inactiveModal.hide();
        }
    }, 380);
}

// Back to Inactive Members List with seamless transition
function backToInactiveList() {
    const detailModalElement = document.getElementById('inactiveMemberDetailModal');
    const detailDialog = detailModalElement.querySelector('.modal-dialog');
    const inactiveModalElement = document.getElementById('inactiveMembersModal');
    const inactiveModal = bootstrap.Modal.getInstance(inactiveModalElement);
    const inactiveDialog = inactiveModalElement.querySelector('.modal-dialog');
    
    const detailModal = bootstrap.Modal.getInstance(detailModalElement);
    
    // Reset inactive modal position
    inactiveDialog.classList.remove('modal-slide-out');
    inactiveDialog.classList.add('modal-slide-in');
    
    // Show inactive modal immediately
    if (inactiveModal) {
        inactiveModal.show();
    } else {
        const newInactiveModal = new bootstrap.Modal(inactiveModalElement, { backdrop: false });
        newInactiveModal.show();
    }
    
    // Slide out detail modal simultaneously
    detailDialog.classList.add('modal-slide-out');
    detailDialog.classList.remove('modal-slide-in');
    
    // Hide detail modal after transition completes
    setTimeout(() => {
        if (detailModal) {
            detailModal.hide();
        }
        inactiveDialog.classList.remove('modal-slide-in');
    }, 380);
}
</script>