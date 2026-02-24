<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Library_model');
        $this->load->helper('url');
        $this->load->library(['session', 'form_validation']);
    }

    /**
     * Get the current tab ID from request
     */
    private function getTabId() {
        // Check query parameter first
        $tab_id = $this->input->get('tab_id');
        if ($tab_id) {
            return $tab_id;
        }
        
        // Check POST parameter
        $tab_id = $this->input->post('tab_id');
        if ($tab_id) {
            return $tab_id;
        }
        
        // Check header (for AJAX requests)
        $headers = $this->input->request_headers();
        if (isset($headers['X-Tab-Id'])) {
            return $headers['X-Tab-Id'];
        }
        
        return null;
    }

    /**
     * Set session data for specific tab
     */
    private function setTabSession($tab_id, $key, $value) {
        $tab_sessions = $this->session->userdata('tab_sessions') ?: [];
        if (!isset($tab_sessions[$tab_id])) {
            $tab_sessions[$tab_id] = [];
        }
        $tab_sessions[$tab_id][$key] = $value;
        $this->session->set_userdata('tab_sessions', $tab_sessions);
    }

    /**
     * Get session data for specific tab
     */
    private function getTabSession($tab_id, $key) {
        $tab_sessions = $this->session->userdata('tab_sessions') ?: [];
        if (isset($tab_sessions[$tab_id][$key])) {
            return $tab_sessions[$tab_id][$key];
        }
        return null;
    }

    /**
     * Get all session data for specific tab
     */
    private function getTabSessionData($tab_id) {
        $tab_sessions = $this->session->userdata('tab_sessions') ?: [];
        return isset($tab_sessions[$tab_id]) ? $tab_sessions[$tab_id] : [];
    }

    /**
     * Clear session data for specific tab
     */
    private function clearTabSession($tab_id) {
        $tab_sessions = $this->session->userdata('tab_sessions') ?: [];
        if (isset($tab_sessions[$tab_id])) {
            unset($tab_sessions[$tab_id]);
            $this->session->set_userdata('tab_sessions', $tab_sessions);
        }
    }

    /**
     * Override _remap to sync active session before every request
     * This ensures each browser tab maintains its own session context
     */
    public function _remap($method = '', $params = array()) {
        // Get tab ID from request
        $tab_id = $this->getTabId();
        
        if ($tab_id) {
            // Sync library_* variables with this tab's session data
            $tab_data = $this->getTabSessionData($tab_id);
            if (!empty($tab_data)) {
                $this->session->set_userdata([
                    'library_user_id' => $tab_data['user_id'] ?? null,
                    'library_username' => $tab_data['username'] ?? null,
                    'library_role' => $tab_data['role'] ?? null
                ]);
            } else {
                // No session for this tab, clear library variables
                $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role']);
            }
        }
        
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        } else {
            show_404();
        }
    }

    /**
     * Determine the active role for this request from multiple sources
     * Priority: Query param > Session determination > Fallback to default
     */
    private function getActiveRole() {
        // Priority 1: Check if role is explicitly passed as query/post parameter
        if ($this->input->get('active_role')) {
            return $this->input->get('active_role');
        }
        if ($this->input->post('active_role')) {
            return $this->input->post('active_role');
        }
        
        // Priority 2: Check request header for role (sent by client JavaScript)
        $header_role = $this->input->request_headers('X-Active-Role');
        if ($header_role) {
            return $header_role;
        }
        
        // Priority 3: Check the current URI path to determine context
        $current_uri = $this->uri->segment(2); // Get the controller method
        if ($current_uri === 'user-dashboard' || 
            $current_uri === 'browse' || 
            $current_uri === 'my-books' || 
            $current_uri === 'history' ||
            $current_uri === 'borrow-book' ||
            $current_uri === 'profile') {
            return 'member';
        }
        if ($current_uri === 'admin-dashboard' || 
            $current_uri === 'books' || 
            $current_uri === 'members' || 
            $current_uri === 'circulation' || 
            $current_uri === 'pending-users' || 
            $current_uri === 'approved-users' ||
            $current_uri === 'approve-user' ||
            $current_uri === 'reject-user' ||
            $current_uri === 'deactivate-user') {
            return 'admin';
        }
        
        // Priority 4: Check the Referer header for context clues
        $referer = $this->input->server('HTTP_REFERER') ?: '';
        if (strpos($referer, 'admin-dashboard') !== false || 
            strpos($referer, 'approved-users') !== false || 
            strpos($referer, 'pending-users') !== false || 
            strpos($referer, 'deactivate-user') !== false ||
            strpos($referer, 'approve-user') !== false || 
            strpos($referer, 'reject-user') !== false ||
            strpos($referer, 'books') !== false ||
            strpos($referer, 'members') !== false ||
            strpos($referer, 'circulation') !== false ||
            strpos($referer, 'active_role=admin') !== false) {
            return 'admin';
        }
        if (strpos($referer, 'user-dashboard') !== false ||
            strpos($referer, 'browse') !== false ||
            strpos($referer, 'my-books') !== false ||
            strpos($referer, 'history') !== false ||
            strpos($referer, 'active_role=member') !== false) {
            return 'member';
        }
        
        // Priority 5: If only one role is logged in, use that
        $admin_id = $this->session->userdata('admin_user_id');
        $member_id = $this->session->userdata('member_user_id');
        
        if ($admin_id && !$member_id) {
            return 'admin';
        }
        if ($member_id && !$admin_id) {
            return 'member';
        }
        
        // Default to member if both present (safer for user)
        if ($member_id && $admin_id) {
            return 'member';
        }
        
        return null;
    }

    /**
     * Helper method to maintain library_* variables based on determined active role
     * This ensures each tab/session maintains its own active user context
     */
    private function syncActiveSessionByRole($active_role) {
        if ($active_role === 'admin') {
            $admin_id = $this->session->userdata('admin_user_id');
            if ($admin_id) {
                $this->session->set_userdata([
                    'library_user_id' => $admin_id,
                    'library_username' => $this->session->userdata('admin_username'),
                    'library_role' => $this->session->userdata('admin_role')
                ]);
                return;
            }
        } else if ($active_role === 'member') {
            $member_id = $this->session->userdata('member_user_id');
            if ($member_id) {
                $this->session->set_userdata([
                    'library_user_id' => $member_id,
                    'library_username' => $this->session->userdata('member_username'),
                    'library_role' => $this->session->userdata('member_role')
                ]);
                return;
            }
        }
        
        // Fallback: no active role set, clear library variables
        $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role']);
    }

    /**
     * Dashboard - restricted to logged in users
     */
    public function dashboard() {
        $admin_id = $this->session->userdata('admin_user_id');
        $member_id = $this->session->userdata('member_user_id');
        $admin_role = $this->session->userdata('admin_role');
        $member_role = $this->session->userdata('member_role');
        
        // Check which role is logged in
        if ($admin_id && $admin_role && ($admin_role === 'admin' || $admin_role === 'librarian')) {
            redirect('library/admin-dashboard');
        } else if ($member_id && $member_role === 'member') {
            redirect('library/user-dashboard');
        } else {
            redirect('library/login');
        }
    }

    /**
     * Admin Dashboard
     */
    public function admin_dashboard() {
        $admin_id = $this->session->userdata('admin_user_id');
        $admin_role = $this->session->userdata('admin_role');
        
        if (!$admin_id || !($admin_role === 'admin' || $admin_role === 'librarian')) {
            redirect('library/login');
        }

        // If member is also logged in, allow access to admin dashboard
        $member_id = $this->session->userdata('member_user_id');

        $data['page_title'] = 'Dashboard';
        $data['stats'] = $this->Library_model->get_dashboard_stats();
        $data['pending_count'] = $this->Library_model->count_pending_users();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/dashboard', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * User Dashboard
     */
    public function user_dashboard() {
        $member_id = $this->session->userdata('member_user_id');
        $member_role = $this->session->userdata('member_role');
        
        if (!$member_id || $member_role !== 'member') {
            redirect('library/login');
        }

        // If admin is also logged in, allow access to user dashboard
        $admin_id = $this->session->userdata('admin_user_id');

        $user_id = $member_id;
        $data['page_title'] = 'My Dashboard';
        $data['user'] = $this->Library_model->get_user($user_id);
        
        // Get member_id from email
        $this->db->where('email', $data['user']['email']);
        $member = $this->db->get('members')->row_array();
        $member_id = $member ? $member['id'] : 0;
        
        // Get user statistics
        // Count borrowed books (not returned)
        $this->db->where('member_id', $member_id);
        $this->db->where('status !=', 'returned');
        $data['borrowed_count'] = $this->db->count_all_results('circulation');
        
        // Count overdue books
        $this->db->where('member_id', $member_id);
        $this->db->where('due_date <', date('Y-m-d'));
        $this->db->where('status', 'borrowed');
        $data['overdue_count'] = $this->db->count_all_results('circulation');
        
        // Total available book copies in library (sum of available quantities)
        $query = $this->db->select_sum('available_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        $data['available_count'] = $result['available_quantity'] ? (int)$result['available_quantity'] : 0;
        
        // Total book copies in library (sum of total quantities)
        $query = $this->db->select_sum('total_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        $data['total_books'] = $result['total_quantity'] ? (int)$result['total_quantity'] : 0;
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/user-dashboard', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Browse Books (Member View)
     */
    public function browse_books() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $data['page_title'] = 'Browse Books';
        $data['books'] = $this->Library_model->get_all_books();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/browse_books', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * View Book Details (Member View)
     */
    public function browse_book_details($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $data['page_title'] = 'Book Details';
        $data['book'] = $this->Library_model->get_book($id);
        
        if (!$data['book']) {
            $this->session->set_flashdata('error', 'Book not found');
            redirect('library/browse');
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/browse_book_details', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * My Borrowed Books
     */
    public function my_books() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
        $data['page_title'] = 'My Borrowed Books';
        
        // Get member_id from user email
        $user = $this->Library_model->get_user($user_id);
        $this->db->where('email', $user['email']);
        $member = $this->db->get('members')->row_array();
        $member_id = $member ? $member['id'] : 0;
        
        // Get borrowed books (not returned)
        $data['borrowed_books'] = $this->db
            ->select('c.*, b.title as book_title, b.author, b.id as book_id')
            ->from('circulation c')
            ->join('books b', 'c.book_id = b.id')
            ->where('c.member_id', $member_id)
            ->where('c.status !=', 'returned')
            ->order_by('c.borrow_date', 'DESC')
            ->get()
            ->result_array();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/my_books', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Borrowing History
     */
    public function history() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
        $data['page_title'] = 'Borrowing History';
        
        // Get member_id from user email
        $user = $this->Library_model->get_user($user_id);
        $this->db->where('email', $user['email']);
        $member = $this->db->get('members')->row_array();
        $member_id = $member ? $member['id'] : 0;

        $per_page = 10;
        $page = (int) $this->input->get('page');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $per_page;

        $total_history = $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->count_all_results();

        $returned_count = $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->where('c.status', 'returned')
            ->count_all_results();

        $borrowed_count = $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->where('c.status', 'borrowed')
            ->count_all_results();
        
        // Get borrowing history (paginated)
        $data['history'] = $this->db
            ->select('c.*, b.title as book_title, b.author, b.id as book_id')
            ->from('circulation c')
            ->join('books b', 'c.book_id = b.id')
            ->where('c.member_id', $member_id)
            ->order_by('c.borrow_date', 'DESC')
            ->limit($per_page, $offset)
            ->get()
            ->result_array();

        $data['history_total'] = $total_history;
        $data['history_returned'] = $returned_count;
        $data['history_borrowed'] = $borrowed_count;
        $data['pagination'] = array(
            'current_page' => $page,
            'per_page' => $per_page,
            'total' => $total_history,
            'total_pages' => (int) ceil($total_history / $per_page),
            'base_url' => site_url('library/history')
        );
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/history', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Borrow a Book
     */
    public function borrow_book($book_id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
        
        // Get user info to find member record
        $user = $this->Library_model->get_user($user_id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('library/browse');
        }

        // Find member record by email (users and members are linked by email)
        $this->db->where('email', $user['email']);
        $member = $this->db->get('members')->row_array();

        // If no member record exists, create one
        if (!$member) {
            $member_data = array(
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'membership_date' => date('Y-m-d'),
                'is_active' => 1
            );
            $this->Library_model->add_member($member_data);
            
            // Get the newly created member
            $this->db->where('email', $user['email']);
            $member = $this->db->get('members')->row_array();
        }

        $member_id = $member['id'];
        
        // Check if book exists and is available
        $book = $this->Library_model->get_book($book_id);
        if (!$book) {
            $this->session->set_flashdata('error', 'Book not found');
            redirect('library/browse');
        }

        if ($book['available_quantity'] <= 0) {
            $this->session->set_flashdata('error', 'This book is currently unavailable');
            redirect('library/browse');
        }

        // Check if user already has this book borrowed
        $this->db->where('member_id', $member_id);
        $this->db->where('book_id', $book_id);
        $this->db->where('status !=', 'returned');
        $existing_borrow = $this->db->get('circulation')->row_array();

        if ($existing_borrow) {
            $this->session->set_flashdata('error', 'You already have this book borrowed');
            redirect('library/browse');
        }

        // Create borrow record
        $borrow_data = array(
            'book_id' => $book_id,
            'member_id' => $member_id,
            'borrow_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+14 days')), // 2 weeks loan period
            'status' => 'borrowed',
            'fine_amount' => 0.00
        );

        // Use the model's borrow_book method which handles transaction
        $result = $this->Library_model->borrow_book($borrow_data);

        if ($result) {
            $this->session->set_flashdata('success', 'Book borrowed successfully! Due date: ' . date('M d, Y', strtotime('+14 days')));
            redirect('library/my-books');
        } else {
            $this->session->set_flashdata('error', 'Failed to borrow book. Please try again.');
            redirect('library/browse');
        }
    }

    /**
     * Return a Borrowed Book
     */
    public function return_book($circulation_id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
        $user = $this->Library_model->get_user($user_id);

        // Get member_id from email
        $this->db->where('email', $user['email']);
        $member = $this->db->get('members')->row_array();
        $member_id = $member ? $member['id'] : 0;

        // Get circulation record
        $circulation = $this->db->where('id', $circulation_id)->get('circulation')->row_array();
        
        if (!$circulation) {
            $this->session->set_flashdata('error', 'Borrow record not found');
            redirect('library/my-books');
        }

        // Verify this circulation belongs to the logged-in user
        if ($circulation['member_id'] != $member_id) {
            $this->session->set_flashdata('error', 'Unauthorized action');
            redirect('library/my-books');
        }

        // Check if already returned
        if ($circulation['status'] === 'returned') {
            $this->session->set_flashdata('error', 'This book has already been returned');
            redirect('library/my-books');
        }

        // Return the book
        $return_date = date('Y-m-d');
        if ($this->Library_model->return_book($circulation_id, $return_date)) {
            $this->session->set_flashdata('success', 'Book returned successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to return book. Please try again.');
        }
        
        redirect('library/my-books');
    }

    /**
     * Clear Fine for a Borrowed Book
     */
    public function clear_fine($circulation_id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
        $user = $this->Library_model->get_user($user_id);

        // Get member_id from email
        $this->db->where('email', $user['email']);
        $member = $this->db->get('members')->row_array();
        $member_id = $member ? $member['id'] : 0;

        // Get circulation record
        $circulation = $this->db->where('id', $circulation_id)->get('circulation')->row_array();
        
        if (!$circulation) {
            $this->session->set_flashdata('error', 'Borrow record not found');
            redirect('library/my-books');
        }

        // Verify this circulation belongs to the logged-in user
        if ($circulation['member_id'] != $member_id) {
            $this->session->set_flashdata('error', 'Unauthorized action');
            redirect('library/my-books');
        }

        // Clear the fine
        $data = array('fine_amount' => 0.00, 'updated_at' => date('Y-m-d H:i:s'));
        if ($this->db->where('id', $circulation_id)->update('circulation', $data)) {
            $this->session->set_flashdata('success', 'Fine cleared successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to clear fine. Please try again.');
        }
        
        redirect('library/my-books');
    }

    /**
     * Login page
     */
    public function login() {
        $data = [];
        
        if ($this->input->post()) {
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('password'));
            $tab_id = $this->input->post('tab_id'); // Get tab ID from form
            
            // Check if user exists
            $this->db->select('*');
            $this->db->from('users');
            $this->db->where('username', $username);
            $query = $this->db->get();
            $user = $query->row_array();
            
            if (!$user) {
                $this->session->set_flashdata('login_error', "User '$username' not found");
                redirect('library/login');
            } else {
                // Check if active
                if ($user['is_active'] != 1) {
                    $this->session->set_flashdata('login_error', 'Your account is pending admin approval. Please wait for the administrator to approve your registration.');
                    redirect('library/login');
                } else {
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        $role = $user['role'];
                        
                        // Store session data for this specific tab
                        if ($tab_id) {
                            $this->setTabSession($tab_id, 'user_id', $user['id']);
                            $this->setTabSession($tab_id, 'username', $user['username']);
                            $this->setTabSession($tab_id, 'role', $user['role']);
                            
                            // Set role-specific global session variables for backward compatibility
                            if ($role === 'admin' || $role === 'librarian') {
                                $this->session->set_userdata([
                                    'admin_user_id' => $user['id'],
                                    'admin_username' => $user['username'],
                                    'admin_role' => $user['role']
                                ]);
                                redirect('library/dashboard?tab_id=' . urlencode($tab_id) . '&active_role=admin');
                            } else {
                                $this->session->set_userdata([
                                    'member_user_id' => $user['id'],
                                    'member_username' => $user['username'],
                                    'member_role' => $user['role']
                                ]);
                                redirect('library/dashboard?tab_id=' . urlencode($tab_id) . '&active_role=member');
                            }
                        } else {
                            // Fallback if no tab_id (shouldn't happen with JavaScript)
                            $this->session->set_flashdata('login_error', 'Session error. Please try again.');
                            redirect('library/login');
                        }
                    } else {
                        $this->session->set_flashdata('login_error', 'Invalid username or password');
                        redirect('library/login');
                    }
                }
            }
        }
        
        $data['error'] = $this->session->flashdata('login_error');
        $this->load->view('library/auth/login', $data);
    }

    /**
     * Register page
     */
    /**
     * AJAX Register
     */
    public function register_ajax() {
        header('Content-Type: application/json');
        $this->security->csrf_verify = FALSE;
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'message' => 'No data provided']);
            return;
        }
        
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');
        
        if (!$this->form_validation->run()) {
            echo json_encode([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }
        
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        
        // Check if username already exists
        $this->db->where('username', $username);
        $username_exists = $this->db->count_all_results('users') > 0;
        
        // Check if email already exists
        $this->db->where('email', $email);
        $email_exists = $this->db->count_all_results('users') > 0;
        
        if ($username_exists && $email_exists) {
            echo json_encode(['success' => false, 'message' => 'Username and email already exist']);
            return;
        } else if ($username_exists) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        } else if ($email_exists) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            return;
        }
        
        $register_data = [
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'username' => $username,
            'email' => $email,
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'role' => 'member',
            'is_active' => 0,
            'created_at' => gmdate('Y-m-d H:i:s')
        ];
        
        if ($this->Library_model->register_user($register_data)) {
            echo json_encode(['success' => true, 'message' => 'Registration successful! Your account is pending admin approval. You will be able to log in once approved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }
    }

    public function register() {
        if ($this->session->userdata('library_user_id')) {
            redirect('library/dashboard');
        }

        $data = [];
        $this->load->view('library/auth/register', $data);
    }

    /**
     * Logout
     */
    public function logout() {
        $tab_id = $this->getTabId();
        
        if ($tab_id) {
            // Get the role from this tab before clearing
            $tab_data = $this->getTabSessionData($tab_id);
            $role = $tab_data['role'] ?? null;
            
            // Clear session data for this specific tab
            $this->clearTabSession($tab_id);
            
            // Check if any other tabs still have sessions for this role
            $tab_sessions = $this->session->userdata('tab_sessions') ?: [];
            $has_other_admin = false;
            $has_other_member = false;
            
            foreach ($tab_sessions as $other_tab_id => $other_data) {
                if ($other_tab_id !== $tab_id && !empty($other_data['role'])) {
                    if ($other_data['role'] === 'admin' || $other_data['role'] === 'librarian') {
                        $has_other_admin = true;
                    } else {
                        $has_other_member = true;
                    }
                }
            }
            
            // Clear global role session variables if no other tabs have that role
            if ($role === 'admin' || $role === 'librarian') {
                if (!$has_other_admin) {
                    $this->session->unset_userdata(['admin_user_id', 'admin_username', 'admin_role']);
                }
            } else {
                if (!$has_other_member) {
                    $this->session->unset_userdata(['member_user_id', 'member_username', 'member_role']);
                }
            }
            
            // Clear library_* variables
            $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role']);
        }
        
        redirect('library/login');
    }

    /**
     * User profile
     */
    public function profile() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $user_id = $this->session->userdata('library_user_id');
        $data['user'] = $this->Library_model->get_user($user_id);
        $data['page_title'] = 'My Profile';
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/profile', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Edit profile
     */
    public function edit_profile() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $user_id = $this->session->userdata('library_user_id');
        $data['user'] = $this->Library_model->get_user($user_id);
        $data['page_title'] = 'Edit Profile';

        // Handle form submission
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('first_name', 'First Name', 'required|max_length[100]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
            } else {
                $update_data = [
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'email' => $this->input->post('email'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->Library_model->update_user($user_id, $update_data)) {
                    $this->session->set_flashdata('success', 'Profile updated successfully!');
                    redirect('library/profile');
                } else {
                    $this->session->set_flashdata('error', 'Failed to update profile. Please try again.');
                }
            }
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/edit_profile', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Change password page
     */
    public function change_password() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $user_id = $this->session->userdata('library_user_id');
        $data['user'] = $this->Library_model->get_user($user_id);
        $data['page_title'] = 'Change Password';

        $this->load->view('library/templates/header', $data);
        $this->load->view('library/change_password', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Change password via AJAX
     */
    public function change_password_ajax() {
        // Disable CSRF check for AJAX
        $this->security->csrf_verify = FALSE;

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'You are not logged in.']);
            exit;
        }

        $user_id = $this->session->userdata('library_user_id');
        $current_password = $this->input->post('current_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        // Validation
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
            exit;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
            exit;
        }

        $user = $this->Library_model->get_user($user_id);

        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit;
        }
          
          
        // Update password
        $update_data = [
            'password' => password_hash($new_password, PASSWORD_BCRYPT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->Library_model->update_user($user_id, $update_data)) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to change password. Please try again.']);
        }
    }

    /**
     * Books Management
     */
    public function books() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Books Management';
        $data['books'] = $this->Library_model->get_all_books();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/books/index', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Add New Book Form
     */
    public function add_book() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Add New Book';
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/books/add', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Insert New Book
     */
    public function insert_book() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        if ($this->input->post()) {
            // Get form data

            
            $isbn = trim($this->input->post('isbn',true));
            $title = trim($this->input->post('title'));
            $author = trim($this->input->post('author'));
            $publisher = trim($this->input->post('publisher'));
            $publication_year = $this->input->post('publication_year');
            $total_quantity = $this->input->post('total_quantity');
            $available_quantity = $this->input->post('available_quantity');
            $description = trim($this->input->post('description'));

            // Validate required fields
            if (empty($isbn) || empty($title) || empty($author)) {
                $this->session->set_flashdata('error', 'ISBN, Title, and Author are required fields');
                redirect('library/books/add');
            }

            // Validate quantities
            if ($available_quantity > $total_quantity) {
                $this->session->set_flashdata('error', 'Available quantity cannot exceed total quantity');
                redirect('library/books/add');
            }

            // Check if ISBN already exists
            $this->db->where('isbn', $isbn);
            $existing_book = $this->db->get('books')->row_array();
            
            if ($existing_book) {
                $this->session->set_flashdata('error', 'A book with this ISBN already exists');
                redirect('library/books/add');
            }

            // Prepare data for insertion
            $data = array(
                'isbn' => $isbn,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'publication_year' => !empty($publication_year) ? $publication_year : NULL,
                'total_quantity' => $total_quantity,
                'available_quantity' => $available_quantity,
                'description' => !empty($description) ? $description : NULL,
                'archived' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Insert book
            if ($this->Library_model->add_book($data)) {
                $this->session->set_flashdata('success', 'Book added successfully');
                redirect('library/books');
            } else {
                $this->session->set_flashdata('error', 'Failed to add book. Please try again.');
                redirect('library/books/add');
            }
        } else {
            redirect('library/books/add');
        }
    }

    /**
     * View Book Details
     */
    public function view_book($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'View Book';
        $data['book'] = $this->Library_model->get_book($id);
        
        if (!$data['book']) {
            $this->session->set_flashdata('error', 'Book not found');
            redirect('library/books');
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/books/view', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Edit Book
     */
    public function edit_book($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Edit Book';
        $data['book'] = $this->Library_model->get_book($id);
        
        if (!$data['book']) {
            $this->session->set_flashdata('error', 'Book not found');
            redirect('library/books');
        }

        // Prevent editing archived books
        if ($data['book']['archived']) {
            $this->session->set_flashdata('error', 'Cannot edit an archived book. Please restore it first.');
            redirect('library/books/view/' . $id);
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/books/edit', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Update Book
     */
    public function update_book($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        if ($this->input->post()) {
            $data = [
                'isbn' => $this->input->post('isbn'),
                'title' => $this->input->post('title'),
                'author' => $this->input->post('author'),
                'publisher' => $this->input->post('publisher'),
                'publication_year' => $this->input->post('publication_year'),
                'description' => $this->input->post('description'),
                'total_quantity' => $this->input->post('total_quantity'),
                'available_quantity' => $this->input->post('available_quantity')
            ];

            if ($this->Library_model->update_book($id, $data)) {
                $this->session->set_flashdata('success', 'Book updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed to update book');
            }
        }
        
        redirect('library/books');
    }

    /**
     * Archive Book
     */
    public function archive_book($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        // Check if book exists
        $book = $this->Library_model->get_book($id);
        if (!$book) {
            $this->session->set_flashdata('error', 'Book not found');
            redirect('library/books');
        }

        // Check if there are borrowed copies
        if ($book['available_quantity'] < $book['total_quantity']) {
            $borrowed_count = $book['total_quantity'] - $book['available_quantity'];
            $this->session->set_flashdata('error', "Cannot archive this book. There are {$borrowed_count} copies currently borrowed. Please wait until all copies are returned.");
            redirect('library/books');
        }

        if ($this->Library_model->archive_book($id)) {
            $this->session->set_flashdata('success', 'Book archived successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to archive book');
        }
        
        redirect('library/books');
    }

    /**
     * Archived Books Page
     */
    public function archived_books() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Archived Books';
        $data['books'] = $this->Library_model->get_archived_books();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/books/archived', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Restore Archived Book
     */
    public function restore_book($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data = ['archived' => 0, 'updated_at' => date('Y-m-d H:i:s')];
        if ($this->Library_model->update_book($id, $data)) {
            $this->session->set_flashdata('success', 'Book restored successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to restore book');
        }
        
        redirect('library/books/archived');
    }

    /**
     * Members Management
     */
    public function members() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Members Management';
        $data['members'] = $this->Library_model->get_all_members();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/members/index', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Add New Member Form
     */
    public function add_member() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Add New Member';
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/members/add', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Insert New Member
     */
    public function insert_member() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        if ($this->input->post()) {
            $first_name = trim($this->input->post('first_name'));
            $last_name = trim($this->input->post('last_name'));
            $email = trim($this->input->post('email'));
            $phone = trim($this->input->post('phone'));
            $membership_date = $this->input->post('membership_date');
            $address = trim($this->input->post('address'));

            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($membership_date)) {
                $this->session->set_flashdata('error', 'First Name, Last Name, and Membership Date are required');
                redirect('library/members/add');
            }

            // Check if email already exists (if provided)
            if (!empty($email)) {
                $this->db->where('email', $email);
                $existing = $this->db->get('members')->row_array();
                if ($existing) {
                    $this->session->set_flashdata('error', 'A member with this email already exists');
                    redirect('library/members/add');
                }
            }

            // Prepare data
            $data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => !empty($email) ? $email : NULL,
                'phone' => !empty($phone) ? $phone : NULL,
                'membership_date' => $membership_date,
                'address' => !empty($address) ? $address : NULL,
                'is_active' => 1
            );

            // Insert member
            if ($this->Library_model->add_member($data)) {
                $this->session->set_flashdata('success', 'Member added successfully');
                redirect('library/members');
            } else {
                $this->session->set_flashdata('error', 'Failed to add member. Please try again.');
                redirect('library/members/add');
            }
        } else {
            redirect('library/members/add');
        }
    }

    /**
     * View Member Details
     */
    public function view_member($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'View Member';
        $data['member'] = $this->Library_model->get_member($id);
        
        if (!$data['member']) {
            $this->session->set_flashdata('error', 'Member not found');
            redirect('library/members');
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/members/view', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Edit Member
     */
    public function edit_member($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Edit Member';
        $data['member'] = $this->Library_model->get_member($id);
        
        if (!$data['member']) {
            $this->session->set_flashdata('error', 'Member not found');
            redirect('library/members');
        }
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/members/edit', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Update Member
     */
    public function update_member($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        if ($this->input->post()) {
            $first_name = trim($this->input->post('first_name'));
            $last_name = trim($this->input->post('last_name'));
            $email = trim($this->input->post('email'));
            $phone = trim($this->input->post('phone'));
            $membership_date = $this->input->post('membership_date');
            $address = trim($this->input->post('address'));

            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($membership_date)) {
                $this->session->set_flashdata('error', 'First Name, Last Name, and Membership Date are required');
                redirect('library/members/edit/' . $id);
            }

            // Check if email exists for another member (if provided)
            if (!empty($email)) {
                $this->db->where('email', $email);
                $this->db->where('id !=', $id);
                $existing = $this->db->get('members')->row_array();
                if ($existing) {
                    $this->session->set_flashdata('error', 'A member with this email already exists');
                    redirect('library/members/edit/' . $id);
                }
            }

            // Prepare data
            $data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => !empty($email) ? $email : NULL,
                'phone' => !empty($phone) ? $phone : NULL,
                'membership_date' => $membership_date,
                'address' => !empty($address) ? $address : NULL
            );

            // Update member
            if ($this->Library_model->update_member($id, $data)) {
                $this->session->set_flashdata('success', 'Member updated successfully');
                redirect('library/members/view/' . $id);
            } else {
                $this->session->set_flashdata('error', 'Failed to update member. Please try again.');
                redirect('library/members/edit/' . $id);
            }
        } else {
            redirect('library/members/edit/' . $id);
        }
    }

    /**
     * Deactivate Member
     */
    public function deactivate_member($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        // Check if member exists
        $member = $this->Library_model->get_member($id);
        if (!$member) {
            $this->session->set_flashdata('error', 'Member not found');
            redirect('library/members');
        }

        // Check if member has active borrowed books
        $this->db->where('member_id', $id);
        $this->db->where('status !=', 'returned');
        $active_borrows = $this->db->count_all_results('circulation');

        if ($active_borrows > 0) {
            $this->session->set_flashdata('error', "Cannot deactivate this member. They have {$active_borrows} book(s) currently borrowed. Please ensure all books are returned first.");
            redirect('library/members');
        }

        // Deactivate member
        if ($this->Library_model->archive_member($id)) {
            $this->session->set_flashdata('success', 'Member deactivated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to deactivate member');
        }
        redirect('library/members');
    }

    /**
     * View Inactive Members
     */
    public function inactive_members() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Inactive Members';
        $data['members'] = $this->db->where('is_active', 0)->order_by('updated_at', 'DESC')->get('members')->result_array();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/members/inactive', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Activate Member
     */
    public function activate_member($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $member = $this->Library_model->get_member($id);
        if (!$member) {
            $this->session->set_flashdata('error', 'Member not found');
            redirect('library/members/inactive');
        }

        // Activate member
        $data = array('is_active' => 1, 'updated_at' => date('Y-m-d H:i:s'));
        if ($this->db->where('id', $id)->update('members', $data)) {
            $this->session->set_flashdata('success', 'Member activated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to activate member');
        }
        redirect('library/members/inactive');
    }

    /**
     * Check if username exists (AJAX)
     */
    public function check_username() {
        header('Content-Type: application/json');
        
        // Disable CSRF for AJAX requests
        $this->security->csrf_verify = FALSE;
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['error' => 'Invalid request']);
            return;
        }
        
        $username = trim($this->input->post('username'));
        
        if (empty($username) || strlen($username) < 3) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $this->db->where('username', $username);
        $exists = $this->db->count_all_results('users') > 0;
        
        echo json_encode(['exists' => $exists]);
    }

    /**
     * Check if email exists (AJAX)
     */
    public function check_email() {
        header('Content-Type: application/json');
        
        // Disable CSRF for AJAX requests
        $this->security->csrf_verify = FALSE;
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['error' => 'Invalid request']);
            return;
        }
        
        $email = trim($this->input->post('email'));
        
        if (empty($email)) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $this->db->where('email', $email);
        $exists = $this->db->count_all_results('users') > 0;
        
        echo json_encode(['exists' => $exists]);
    }

    /**
     * Circulation Management
     */
    public function circulation() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Circulation Management';
        $data['circulations'] = $this->Library_model->get_all_circulations();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/circulation/index', $data);
        $this->load->view('library/templates/footer');
    }

    // ========================================
    // USER APPROVAL MANAGEMENT
    // ========================================

    /**
     * Pending Users - Admin approval page
     */
    public function pending_users() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Pending User Approvals';
        $data['pending_users'] = $this->Library_model->get_pending_users();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/users/pending', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Approved Users - List of approved users
     */
    public function approved_users() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Approved Users';
        $data['approved_users'] = $this->Library_model->get_approved_users();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/users/approved', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Approve a pending user
     */
    public function approve_user($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $user = $this->Library_model->get_user($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('library/pending-users');
        }

        if ($this->Library_model->approve_user($id)) {
            $this->session->set_flashdata('success', 'User "' . $user['username'] . '" has been approved successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to approve user');
        }
        
        redirect('library/pending-users');
    }

    /**
     * Reject a pending user
     */
    public function reject_user($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $user = $this->Library_model->get_user($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('library/pending-users');
        }

        // Prevent rejecting admin accounts
        if ($user['role'] === 'admin') {
            $this->session->set_flashdata('error', 'Cannot reject an admin account');
            redirect('library/pending-users');
        }

        if ($this->Library_model->reject_user($id)) {
            $this->session->set_flashdata('success', 'User "' . $user['username'] . '" has been rejected and removed.');
        } else {
            $this->session->set_flashdata('error', 'Failed to reject user');
        }
        
        redirect('library/pending-users');
    }

    /**
     * Deactivate an approved user
     */
    public function deactivate_user($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $user = $this->Library_model->get_user($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('library/approved-users');
        }

        // Prevent deactivating admin accounts
        if ($user['role'] === 'admin') {
            $this->session->set_flashdata('error', 'Cannot deactivate an admin account');
            redirect('library/approved-users');
        }

        if ($this->Library_model->deactivate_user($id)) {
            $this->session->set_flashdata('success', 'User "' . $user['username'] . '" has been deactivated.');
        } else {
            $this->session->set_flashdata('error', 'Failed to deactivate user');
        }
        
        redirect('library/approved-users');
    }

}
?>
