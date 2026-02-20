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
     * Dashboard - restricted to logged in users
     */
    public function dashboard() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        
        // Redirect based on role
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        } else {
            redirect('library/user-dashboard');
        }
    }

    /**
     * Admin Dashboard
     */
    public function admin_dashboard() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Dashboard';
        $data['stats'] = $this->Library_model->get_dashboard_stats();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/dashboard', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * User Dashboard
     */
    public function user_dashboard() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        $user_id = $this->session->userdata('library_user_id');
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
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('library_user_id')) {
            redirect('library/dashboard');
        }

        $data = [];
        
        if ($this->input->post()) {
            $username = trim($this->input->post('username'));
            $password = trim($this->input->post('password'));
            
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
                    $this->session->set_flashdata('login_error', 'Your account has been deactivated');
                    redirect('library/login');
                } else {
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        $this->session->set_userdata([
                            'library_user_id' => $user['id'],
                            'library_username' => $user['username'],
                            'library_role' => $user['role']
                        ]);
                        redirect('library/dashboard');
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
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->Library_model->register_user($register_data)) {
            echo json_encode(['success' => true, 'message' => 'Registration successful! Redirecting to login...']);
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
        $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role']);
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

}
?>
