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
            // We have a tab_id from the request, use it
            // Method 1: Try direct session keys first (most reliable)
            $user_id = $this->session->userdata('library_tab_' . $tab_id . '_user_id');
            $username = $this->session->userdata('library_tab_' . $tab_id . '_username');
            $role = $this->session->userdata('library_tab_' . $tab_id . '_role');
            
            if ($user_id && $username && $role) {
                // Found user data in direct session keys
                $this->session->set_userdata([
                    'library_user_id' => $user_id,
                    'library_username' => $username,
                    'library_role' => $role,
                    'library_current_tab' => $tab_id  // Store current tab for future reference
                ]);
            } else {
                // Method 2: Fallback to tab_sessions array lookup
                $tab_data = $this->getTabSessionData($tab_id);
                if (!empty($tab_data)) {
                    $this->session->set_userdata([
                        'library_user_id' => $tab_data['user_id'] ?? null,
                        'library_username' => $tab_data['username'] ?? null,
                        'library_role' => $tab_data['role'] ?? null,
                        'library_current_tab' => $tab_id  // Store current tab for future reference
                    ]);
                } else {
                    // No session for this tab, clear library variables
                    $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role']);
                }
            }
        } else {
            // No tab_id in request - try to recover from previous tab or session
            $current_user_id = $this->session->userdata('library_user_id');
            $current_tab = $this->session->userdata('library_current_tab');
            
            if ($current_user_id) {
                // User is already logged in this session, preserve the session data
                // This handles cases where tab_id is temporarily missing (e.g., manual URL edits)
                // Keep existing library_* variables as-is
            } else {
                // No valid session or tab_id, clear library variables
                $this->session->unset_userdata(['library_user_id', 'library_username', 'library_role', 'library_current_tab']);
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
    // private function getActiveRole() {
    //     // Priority 1: Check if role is explicitly passed as query/post parameter
    //     if ($this->input->get('active_role')) {
    //         return $this->input->get('active_role');
    //     }
    //     if ($this->input->post('active_role')) {
    //         return $this->input->post('active_role');
    //     }
        
    //     // Priority 2: Check request header for role (sent by client JavaScript)
    //     $header_role = $this->input->request_headers('X-Active-Role');
    //     if ($header_role) {
    //         return $header_role;
    //     }
        
    //     // Priority 3: Check the current URI path to determine context
    //     $current_uri = $this->uri->segment(2); // Get the controller method
    //     if ($current_uri === 'user-dashboard' || 
    //         $current_uri === 'browse' || 
    //         $current_uri === 'my-books' || 
    //         $current_uri === 'history' ||
    //         $current_uri === 'borrow-book' ||
    //         $current_uri === 'profile') {
    //         return 'member';
    //     }
    //     if ($current_uri === 'admin-dashboard' || 
    //         $current_uri === 'books' || 
    //         $current_uri === 'members' || 
    //         $current_uri === 'circulation' || 
    //         $current_uri === 'pending-users' || 
    //         $current_uri === 'approved-users' ||
    //         $current_uri === 'approve-user' ||
    //         $current_uri === 'reject-user' ||
    //         $current_uri === 'deactivate-user') {
    //         return 'admin';
    //     }
        
    //     // Priority 4: Check the Referer header for context clues
    //     $referer = $this->input->server('HTTP_REFERER') ?: '';
    //     if (strpos($referer, 'admin-dashboard') !== false || 
    //         strpos($referer, 'approved-users') !== false || 
    //         strpos($referer, 'pending-users') !== false || 
    //         strpos($referer, 'deactivate-user') !== false ||
    //         strpos($referer, 'approve-user') !== false || 
    //         strpos($referer, 'reject-user') !== false ||
    //         strpos($referer, 'books') !== false ||
    //         strpos($referer, 'members') !== false ||
    //         strpos($referer, 'circulation') !== false ||
    //         strpos($referer, 'active_role=admin') !== false) {
    //         return 'admin';
    //     }
    //     if (strpos($referer, 'user-dashboard') !== false ||
    //         strpos($referer, 'browse') !== false ||
    //         strpos($referer, 'my-books') !== false ||
    //         strpos($referer, 'history') !== false ||
    //         strpos($referer, 'active_role=member') !== false) {
    //         return 'member';
    //     }
        
    //     // Priority 5: If only one role is logged in, use that
    //     $admin_id = $this->session->userdata('admin_user_id');
    //     $member_id = $this->session->userdata('member_user_id');
        
    //     if ($admin_id && !$member_id) {
    //         return 'admin';
    //     }
    //     if ($member_id && !$admin_id) {
    //         return 'member';
    //     }
        
    //     // Default to member if both present (safer for user)
    //     if ($member_id && $admin_id) {
    //         return 'member';
    //     }
        
    //     return null;
    // }

    /**
     * Dashboard - restricted to logged in users
     */
    public function dashboard() {
        // Use only tab-specific library_* variables for user context
        // These are synced per tab via _remap to prevent multi-user conflicts
        $library_user_id = $this->session->userdata('library_user_id');
        $library_role = $this->session->userdata('library_role');
        
        // Redirect based on the current tab's user role
        if (!$library_user_id || !$library_role) {
            redirect('library/login');
        }
        
        if ($library_role === 'admin' || $library_role === 'librarian') {
            redirect('library/admin-dashboard');
        } else if ($library_role === 'member') {
            redirect('library/user-dashboard');
        } else {
            redirect('library/login');
        }
    }

    /**
     * Admin Dashboard
     */
    public function admin_dashboard() {
        // Use only tab-specific library_* variables for user context
        $library_user_id = $this->session->userdata('library_user_id');
        $library_role = $this->session->userdata('library_role');
        
        if (!$library_user_id || !($library_role === 'admin' || $library_role === 'librarian')) {
            redirect('library/login');
        }

        $data['page_title'] = 'Dashboard';
        $data['stats'] = $this->Library_model->get_dashboard_stats();
        $data['pending_count'] = $this->Library_model->count_pending_users();
        $data['overdue_books'] = $this->Library_model->get_overdue_books();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/dashboard', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * User Dashboard
     */
    public function user_dashboard() {
        // Use only tab-specific library_* variables for user context
        $library_user_id = $this->session->userdata('library_user_id');
        $library_role = $this->session->userdata('library_role');
        
        if (!$library_user_id || $library_role !== 'member') {
            redirect('library/login');
        }

        $user_id = $library_user_id;
        $data['page_title'] = 'My Dashboard';
        $data['user'] = $this->Library_model->get_user($user_id);
        
        // Get member_id from email
        $member = $this->Library_model->get_member_by_email($data['user']['email']);
        $member_id = $member ? $member['id'] : 0;
        
        // Get user statistics
        $data['borrowed_count'] = $this->Library_model->get_user_borrowed_count($member_id);
        $data['overdue_count'] = $this->Library_model->get_user_overdue_count($member_id);
        $data['available_count'] = $this->Library_model->get_total_available_books();
        $data['total_books'] = $this->Library_model->get_total_books_quantity();
        
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
     * Get Book Details (AJAX)
     */
    public function get_book_details($id) {
        $this->output->set_content_type('application/json');
        
        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $book = $this->Library_model->get_book($id);
        
        if ($book) {
            echo json_encode(['success' => true, 'book' => $book]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
        exit;
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
        $member = $this->Library_model->get_member_by_email($user['email']);
        $member_id = $member ? $member['id'] : 0;
        
        // Get borrowed books (not returned)
        $data['borrowed_books'] = $this->Library_model->get_user_borrowed_books($member_id);
        
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
        $member = $this->Library_model->get_member_by_email($user['email']);
        $member_id = $member ? $member['id'] : 0;

        $per_page = 10;
        $page = (int) $this->input->get('page');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $per_page;

        // Get statistics and history from model
        $total_history = $this->Library_model->count_user_history($member_id);
        $returned_count = $this->Library_model->count_user_returned($member_id);
        $borrowed_count = $this->Library_model->count_user_borrowed($member_id);
        $data['history'] = $this->Library_model->get_user_history($member_id, $per_page, $offset);

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
    /**
     * Borrow a Book
     * @param int $book_id The book ID to borrow
     * @param int $days Number of days to borrow (default 14)
     */
    public function borrow_book($book_id, $days = 14) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role === 'admin' || $role === 'librarian') {
            redirect('library/admin-dashboard');
        }

        // Validate days (minimum 1, maximum 14)
        $days = max(1, min(14, intval($days)));

        $user_id = $this->session->userdata('library_user_id');
        
        // Get user info to find member record
        $user = $this->Library_model->get_user($user_id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('library/browse');
        }

        // Find member record by email (users and members are linked by email)
        $member = $this->Library_model->get_member_by_email($user['email']);

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
            $member = $this->Library_model->get_member_by_email($user['email']);
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
        $existing_borrow = $this->Library_model->has_borrowed_book($member_id, $book_id);

        if ($existing_borrow) {
            $this->session->set_flashdata('error', 'You already have this book borrowed');
            redirect('library/browse');
        }

        // Calculate due date based on selected days
        $due_date = date('Y-m-d', strtotime("+{$days} days"));

        // Create borrow record
        $borrow_data = array(
            'book_id' => $book_id,
            'member_id' => $member_id,
            'borrow_date' => date('Y-m-d'),
            'due_date' => $due_date,
            'status' => 'borrowed'
        );

        // Use the model's borrow_book method which handles transaction
        $result = $this->Library_model->borrow_book($borrow_data);

        if ($result) {
            $this->session->set_flashdata('success', 'Book borrowed successfully for ' . $days . ' days! Due date: ' . date('M d, Y', strtotime($due_date)));
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
        $member = $this->Library_model->get_member_by_email($user['email']);
        $member_id = $member ? $member['id'] : 0;

        // Get circulation record
        $circulation = $this->Library_model->get_circulation_record($circulation_id);
        
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
        $member = $this->Library_model->get_member_by_email($user['email']);
        $member_id = $member ? $member['id'] : 0;

        // Get circulation record
        $circulation = $this->Library_model->get_circulation_record($circulation_id);
        
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
        if ($this->Library_model->clear_fine($circulation_id)) {
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
            $user = $this->Library_model->get_user_by_username($username);
            
            if (!$user) {
                $this->session->set_flashdata('login_error', "Wrong username");
                redirect('library/login');
            } else {
                // Check if active
                if ($user['is_active'] != 1) {
                    $this->session->set_flashdata('login_error', 'Your account is pending approval. Please wait for admin to approve your registration.');
                    redirect('library/login');
                } else {
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        $role = $user['role'];
                        
                        // Store session data for this specific tab
                        if ($tab_id) {
                            // Method 1: Store in tab_sessions array (for convenience)
                            $this->setTabSession($tab_id, 'user_id', $user['id']);
                            $this->setTabSession($tab_id, 'username', $user['username']);
                            $this->setTabSession($tab_id, 'role', $user['role']);
                            
                            // Method 2: Also store directly in session with tab_id as key (for redundancy and reliability)
                            $this->session->set_userdata('library_tab_' . $tab_id . '_user_id', $user['id']);
                            $this->session->set_userdata('library_tab_' . $tab_id . '_username', $user['username']);
                            $this->session->set_userdata('library_tab_' . $tab_id . '_role', $user['role']);
                            
                            // The _remap method will sync this to library_* variables per tab before each request
                            // Include tab_id in the redirect to ensure proper tab identification across navigation
                            redirect('library/dashboard?tab_id=' . urlencode($tab_id));
                        } else {
                            // Fallback if no tab_id (shouldn't happen with JavaScript)
                            $this->session->set_flashdata('login_error', 'Something went wrong. Please try again.');
                            redirect('library/login');
                        }
                    } else {
                        $this->session->set_flashdata('login_error', 'Wrong password');
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
                'message' => 'Please check your information and try again',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }
        
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        
        // Check if username already exists
        $username_exists = $this->Library_model->username_exists($username);
        
        // Check if email already exists
        $email_exists = $this->Library_model->email_exists($email);
        
        if ($username_exists && $email_exists) {
            echo json_encode(['success' => false, 'message' => 'Username and email already taken']);
            return;
        } else if ($username_exists) {
            echo json_encode(['success' => false, 'message' => 'Username already taken']);
            return;
        } else if ($email_exists) {
            echo json_encode(['success' => false, 'message' => 'Email already taken']);
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
            echo json_encode(['success' => true, 'message' => 'Account created! Waiting for admin approval. You can login once approved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
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
     * Forgot Password
     */
    public function forgot_password() {
        if ($this->session->userdata('library_user_id')) {
            redirect('library/dashboard');
        }

        $data = [];

        if ($this->input->post()) {
            $email = trim($this->input->post('email'));

            // Check if email exists
            $user = $this->Library_model->get_user_by_email($email);

            if (!$user) {
                $data['error'] = 'No account found with this email address';
            } else {
                // Generate reset token
                $token = $this->Library_model->generate_reset_token($email);
                $reset_link = site_url('library/reset-password/' . $token);

                // Send email with reset link
                $this->load->library('email');
                
                $this->email->from('noreply@library.local', 'Library Management System');
                $this->email->to($email);
                $this->email->subject('Password Reset Request - Library Management System');
                
                // Create HTML email with professional design
                $html_message = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <!-- Header -->
                        <div style="background: linear-gradient(135deg, #0066cc 0%, #004499 100%); color: white; padding: 30px; text-align: center;">
                            <h1 style="margin: 0; font-size: 28px;">Library Management System</h1>
                            <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">Password Reset Request</p>
                        </div>
                        
                        <!-- Content -->
                        <div style="padding: 40px 30px;">
                            <p style="margin-top: 0;">Hello <strong>' . htmlspecialchars($user['first_name']) . '</strong>,</p>
                            
                            <p>You requested a password reset for your Library account. Click the button below to reset your password.</p>
                            
                            <p style="text-align: center; margin: 35px 0;">
                                <a href="' . $reset_link . '" style="display: inline-block; background: linear-gradient(135deg, #0066cc 0%, #004499 100%); color: white; padding: 14px 40px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; transition: transform 0.2s;">
                                    Reset Password
                                </a>
                            </p>
                            
                            <p style="color: #666; font-size: 13px; text-align: center;">
                                This link will expire in <strong>1 hour</strong>
                            </p>
                            
                            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
                            
                            <p style="color: #999; font-size: 12px;">If you did not request this password reset, please ignore this email or contact support if you have concerns.</p>
                        </div>
                        
                        <!-- Footer -->
                        <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #ddd;">
                            <p style="margin: 0; color: #666; font-size: 12px;">
                                &copy; 2026 Library Management System. All rights reserved.
                            </p>
                        </div>
                    </div>
                </body>
                </html>
                ';
                
                $this->email->set_mailtype('html');
                $this->email->message($html_message);
                
                if ($this->email->send()) {
                    $data['success'] = 'Password reset link has been sent to your email. Please check your inbox.';
                } else {
                    $data['error'] = 'Failed to send reset email. Please try again later.';
                    // Log the email error
                    log_message('error', 'Email error: ' . $this->email->print_debugger());
                }
            }
        }

        $this->load->view('library/auth/forgot_password', $data);
    }

    /**
     * Reset Password
     */
    public function reset_password($token = null) {
        if ($this->session->userdata('library_user_id')) {
            redirect('library/dashboard');
        }

        if (!$token) {
            $this->session->set_flashdata('error', 'Invalid reset link');
            redirect('library/login');
        }

        // Verify token
        $user = $this->Library_model->verify_reset_token($token);

        if (!$user) {
            $this->session->set_flashdata('error', 'Reset link has expired. Please try again.');
            redirect('library/forgot-password');
        }

        $data = ['token' => $token];

        if ($this->input->post()) {
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');

            // Validate passwords
            if (empty($password) || empty($password_confirm)) {
                $data['error'] = 'Please fill in all fields';
            } elseif (strlen($password) < 6) {
                $data['error'] = 'Password must be at least 6 characters long';
            } elseif ($password !== $password_confirm) {
                $data['error'] = 'Passwords do not match';
            } else {
                // Reset the password
                if ($this->Library_model->reset_password($token, $password)) {
                    $this->session->set_flashdata('success', 'Password has been reset successfully. You can now login.');
                    redirect('library/login');
                } else {
                    $data['error'] = 'Failed to reset password. Please try again.';
                }
            }
        }

        $this->load->view('library/auth/reset_password', $data);
    }

    /**
     * Logout
     */
    public function logout() {
        $tab_id = $this->getTabId();
        
        if ($tab_id) {
            // Clear session data for this specific tab only
            // Since each tab has independent user context, logout only affects this tab
            
            // Clear direct session keys
            $this->session->unset_userdata([
                'library_tab_' . $tab_id . '_user_id',
                'library_tab_' . $tab_id . '_username',
                'library_tab_' . $tab_id . '_role',
                'library_user_id',
                'library_username',
                'library_role'
            ]);
            
            // Also clear from tab_sessions array
            $this->clearTabSession($tab_id);
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

            
            
            $title = trim($this->input->post('title',true));
            $author = trim($this->input->post('author',true));
            $publisher = trim($this->input->post('publisher',true));
            $publication_year = $this->input->post('publication_year', true);
            $total_quantity = $this->input->post('total_quantity', true);
            $available_quantity = $this->input->post('available_quantity', true);
            $description = trim($this->input->post('description',true));

            // Validate required fields
            if (empty($title) || empty($author)) {
                $this->session->set_flashdata('error', 'Title and Author are required fields');
                redirect('library/books/add');
            }

            // Validate quantities
            if ($available_quantity > $total_quantity) {
                $this->session->set_flashdata('error', 'Available quantity cannot exceed total quantity');
                redirect('library/books/add');
            }



            // Prepare data for insertion
            $data = array(
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
     * AJAX: Get Book Edit Form Data as JSON
     */
    public function get_book_edit($id) {
        $this->output->set_content_type('application/json');
        
        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $book = $this->Library_model->get_book($id);
        
        if (!$book) {
            echo json_encode([
                'success' => false,
                'message' => 'Book not found'
            ]);
            exit;
        }

        if ($book['archived']) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot edit an archived book. Please restore it first.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'book' => $book
        ]);
        exit;
    }

    /**
     * AJAX: Update Book
     */
    public function update_book($id = null) {
        // Don't check is_ajax_request for fetch compatibility, check Content-Type header instead
        $content_type = $this->input->server('CONTENT_TYPE');
        
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($this->input->post()) {
            $book = $this->Library_model->get_book($id);
            
            if (!$book) {
                echo json_encode(['success' => false, 'message' => 'Book not found']);
                exit;
            }

            $data = [
                'title' => $this->input->post('title'),
                'author' => $this->input->post('author'),
                'publisher' => $this->input->post('publisher'),
                'publication_year' => $this->input->post('publication_year'),
                'description' => $this->input->post('description'),
                'total_quantity' => $this->input->post('total_quantity'),
                'available_quantity' => $this->input->post('available_quantity')
            ];

            if ($this->Library_model->update_book($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Book updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update book'
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No data provided']);
        }
        exit;
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
                $existing = $this->Library_model->get_member_by_email($email);
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
                $existing = $this->Library_model->get_member_by_email_excluding($email, $id);
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
        $active_borrows = $this->Library_model->count_active_borrows($id);

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
     * AJAX: Get Member Details
     */
    public function get_member_details($id) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $member = $this->Library_model->get_member($id);
        
        if ($member) {
            echo json_encode(['success' => true, 'member' => $member]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
        }
        exit;
    }

    /**
     * AJAX: Get Member for Edit
     */
    public function get_member_edit($id) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $member = $this->Library_model->get_member($id);
        
        if ($member) {
            echo json_encode(['success' => true, 'member' => $member]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
        }
        exit;
    }

    /**
     * AJAX: Update Member
     */
    public function update_member_ajax($id = null) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $member = $this->Library_model->get_member($id);
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
            exit;
        }

        if ($this->input->post()) {
            $first_name = trim($this->input->post('first_name'));
            $last_name = trim($this->input->post('last_name'));
            $email = trim($this->input->post('email'));
            $membership_date = $this->input->post('membership_date');

            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($membership_date)) {
                echo json_encode(['success' => false, 'message' => 'First Name, Last Name, and Membership Date are required']);
                exit;
            }

            // Check if email exists for another member (if provided)
            if (!empty($email)) {
                $existing = $this->Library_model->get_member_by_email_excluding($email, $id);
                if ($existing) {
                    echo json_encode(['success' => false, 'message' => 'A member with this email already exists']);
                    exit;
                }
            }

            $data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'membership_date' => $membership_date
            ];

            if ($this->Library_model->update_member($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Member updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update member']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No data provided']);
        }
        exit;
    }

    /**
     * AJAX: Deactivate Member
     */
    public function deactivate_member_ajax($id) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Check if member exists
        $member = $this->Library_model->get_member($id);
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
            exit;
        }

        // Check if member has active borrowed books
        $active_borrows = $this->Library_model->count_active_borrows($id);
        if ($active_borrows > 0) {
            echo json_encode(['success' => false, 'message' => "Cannot deactivate this member. They have {$active_borrows} book(s) currently borrowed. Please ensure all books are returned first."]);
            exit;
        }

        // Deactivate member
        if ($this->Library_model->archive_member($id)) {
            echo json_encode(['success' => true, 'message' => 'Member deactivated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to deactivate member']);
        }
        exit;
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
        $data['members'] = $this->Library_model->get_inactive_members();
        
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
        if ($this->Library_model->activate_member($id)) {
            $this->session->set_flashdata('success', 'Member activated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to activate member');
        }
        redirect('library/members/inactive');
    }

    /**
     * AJAX: Get Inactive Members
     */
    public function get_inactive_members_ajax() {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $members = $this->Library_model->get_inactive_members();
        echo json_encode(['success' => true, 'members' => $members]);
        exit;
    }

    /**
     * AJAX: Get Add Member Form
     */
    public function get_add_member_form() {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * AJAX: Insert Member
     */
    public function insert_member_ajax() {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($this->input->post()) {
            $first_name = trim($this->input->post('first_name'));
            $last_name = trim($this->input->post('last_name'));
            $email = trim($this->input->post('email'));
            $membership_date = $this->input->post('membership_date');
            $username = trim($this->input->post('username'));
            $password = $this->input->post('password');
            $confirm_password = $this->input->post('confirm_password');

            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($membership_date)) {
                echo json_encode(['success' => false, 'message' => 'First Name, Last Name, and Membership Date are required']);
                exit;
            }

            // Validate credentials
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Username and Password are required']);
                exit;
            }

            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email is required']);
                exit;
            }

            if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
                exit;
            }

            if ($password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
                exit;
            }

            // Check if email exists (in users or members)
            $this->db->select('email')->from('users')->where('email', $email);
            $user_result = $this->db->get()->result();
            if (!empty($user_result)) {
                echo json_encode(['success' => false, 'message' => 'Email is already registered']);
                exit;
            }

            $existing_member = $this->Library_model->get_member_by_email($email);
            if ($existing_member) {
                echo json_encode(['success' => false, 'message' => 'A member with this email already exists']);
                exit;
            }

            // Check if username exists
            $this->db->select('username')->from('users')->where('username', $username);
            $username_result = $this->db->get()->result();
            if (!empty($username_result)) {
                echo json_encode(['success' => false, 'message' => 'Username already exists']);
                exit;
            }

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user account
            $user_data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'role' => 'member',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->db->insert('users', $user_data)) {
                echo json_encode(['success' => false, 'message' => 'Failed to create user account']);
                exit;
            }

            // Insert member record
            $member_data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'membership_date' => $membership_date,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Library_model->add_member($member_data)) {
                echo json_encode(['success' => true, 'message' => 'Member added successfully with account credentials']);
            } else {
                // Rollback user creation if member insertion fails
                $this->db->delete('users', ['username' => $username]);
                echo json_encode(['success' => false, 'message' => 'Failed to add member']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No data provided']);
        }
        exit;
    }

    /**
     * AJAX: Activate Member
     */
    public function activate_member_ajax($id) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $member = $this->Library_model->get_member($id);
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found']);
            exit;
        }

        if ($this->Library_model->activate_member($id)) {
            echo json_encode(['success' => true, 'message' => 'Member activated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to activate member']);
        }
        exit;
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
        
        $exists = $this->Library_model->username_exists($username);
        
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
        
        $exists = $this->Library_model->email_exists($email);
        
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

    /**
     * Archive Circulation Record
     */
    public function archive_circulation($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        // Get circulation record
        $circulation = $this->Library_model->get_circulation_record($id);
        if (!$circulation) {
            $this->session->set_flashdata('error', 'Circulation record not found');
            redirect('library/circulation');
        }

        // Archive the record
        if ($this->Library_model->archive_circulation($id)) {
            $this->session->set_flashdata('success', 'Circulation record archived successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to archive circulation record');
        }
        
        redirect('library/circulation');
    }

    /**
     * View Archived Circulation Records
     */
    public function archived_circulations() {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        $data['page_title'] = 'Archived Circulations';
        $data['circulations'] = $this->Library_model->get_archived_circulations();
        
        $this->load->view('library/templates/header', $data);
        $this->load->view('library/circulation/archived', $data);
        $this->load->view('library/templates/footer');
    }

    /**
     * Restore Archived Circulation Record
     */
    public function restore_circulation($id) {
        if (!$this->session->userdata('library_user_id')) {
            redirect('library/login');
        }

        $role = $this->session->userdata('library_role');
        if ($role !== 'admin' && $role !== 'librarian') {
            redirect('library/user-dashboard');
        }

        // Get circulation record
        $circulation = $this->Library_model->get_circulation_record($id);
        if (!$circulation) {
            $this->session->set_flashdata('error', 'Circulation record not found');
            redirect('library/circulation/archived');
        }

        // Restore the record
        if ($this->Library_model->restore_circulation($id)) {
            $this->session->set_flashdata('success', 'Circulation record restored successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to restore circulation record');
        }
        
        redirect('library/circulation/archived');
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

    /**
     * Get Book Details via AJAX (for members and guests)
     */
    public function get_book_details_ajax($id) {
        $this->output->set_content_type('application/json');

        if (!$this->session->userdata('library_user_id')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $book = $this->Library_model->get_book($id);
        
        if ($book) {
            echo json_encode(['success' => true, 'book' => $book]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
        exit;
    }

}
?>
