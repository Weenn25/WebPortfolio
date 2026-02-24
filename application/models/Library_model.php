<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Authenticate user login
     */
    public function login_user($username, $password) {
        $user = $this->db->where('username', $username)
                         ->where('is_active', 1)
                         ->get('users')
                         ->row_array();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Register new user
     */
    public function register_user($data) {
        // Ensure created_at is always in UTC
        if (!isset($data['created_at'])) {
            $data['created_at'] = gmdate('Y-m-d H:i:s');
        }
        return $this->db->insert('users', $data);
    }

    /**
     * Get user by ID
     */
    public function get_user($id) {
        return $this->db->where('id', $id)->get('users')->row_array();
    }

    /**
     * Update user information
     */
    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats() {
        $stats = [];
        
        // Total books - sum of all book quantities (excluding archived)
        $query = $this->db->select_sum('total_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        $stats['total_books'] = $result['total_quantity'] ? (int)$result['total_quantity'] : 0;
        
        // Available books - sum of available quantities (excluding archived)
        $query = $this->db->select_sum('available_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        $stats['available_books'] = $result['available_quantity'] ? (int)$result['available_quantity'] : 0;
        
        // Total members (active only)
        $stats['total_members'] = $this->db->where('is_active', 1)->count_all_results('members');
        
        // Borrowed books (currently borrowed, not returned)
        $stats['borrowed_books'] = $this->db->where('status !=', 'returned')->count_all_results('circulation');
        
        // Overdue books (borrowed and past due date)
        $stats['overdue_books'] = $this->db->where('status', 'borrowed')
                                           ->where('due_date <', date('Y-m-d'))
                                           ->count_all_results('circulation');
        
        return $stats;
    }

    // ========================================
    // BOOKS MANAGEMENT
    // ========================================

    /**
     * Get all books with pagination
     */
    public function get_books($limit = 10, $offset = 0) {
        return $this->db->limit($limit, $offset)
                        ->order_by('id', 'DESC')
                        ->get('books')
                        ->result_array();
    }

    /**
     * Get single book by ID
     */
    public function get_book($id) {
        return $this->db->where('id', $id)->get('books')->row_array();
    }

    /**
     * Count all books
     */
    public function count_books() {
        return $this->db->count_all('books');
    }

    /**
     * Add new book
     */
    public function add_book($data) {

    // if condititon 

    
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('books', $data);
    }

    /**
     * Update book
     */
    public function update_book($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('books', $data);
    }

    /**
     * Archive book by ID
     */
    public function archive_book($id) {
        return $this->db->where('id', $id)
                        ->update('books', ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Delete book
     */
    public function delete_book($id) {
        return $this->db->where('id', $id)->delete('books');
    }

    // ========================================
    // MEMBERS MANAGEMENT
    // ========================================

    /**
     * Get all members with pagination
     */
    public function get_members($limit = 10, $offset = 0) {
        return $this->db->limit($limit, $offset)
                        ->order_by('id', 'DESC')
                        ->get('members')
                        ->result_array();
    }

    /**
     * Get single member by ID
     */
    public function get_member($id) {
        return $this->db->where('id', $id)->get('members')->row_array();
    }

    /**
     * Count all active members
     */
    public function count_members() {
        return $this->db->where('is_active', 1)->count_all_results('members');
    }

    /**
     * Add new member
     */
    public function add_member($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('members', $data);
    }

    /**
     * Update member
     */
    public function update_member($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('members', $data);
    }

    /**
     * Archive member
     */
    public function archive_member($id) {
        return $this->db->where('id', $id)
                        ->update('members', ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Delete member
     */
    public function delete_member($id) {
        return $this->db->where('id', $id)->delete('members');
    }

    // ========================================
    // CIRCULATION MANAGEMENT
    // ========================================

    /**
     * Borrow a book
     */
    public function borrow_book($data) {
        $this->db->trans_start();
        
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('circulation', $data);
        $circulation_id = $this->db->insert_id();
        
        // Decrease available quantity
        $this->db->where('id', $data['book_id'])
                 ->set('available_quantity', 'available_quantity - 1', FALSE)
                 ->update('books');
        
        $this->db->trans_complete();
        
        return ($this->db->trans_status() === FALSE) ? false : $circulation_id;
    }

    /**
     * Return a borrowed book
     */
    public function return_book($circulation_id, $return_date) {
        $this->db->trans_start();
        
        $circulation = $this->get_circulation_record($circulation_id);
        
        $this->db->where('id', $circulation_id)
                 ->update('circulation', [
                     'return_date' => $return_date, 
                     'status' => 'returned',
                     'updated_at' => date('Y-m-d H:i:s')
                 ]);
        
        // Increase available quantity
        $this->db->where('id', $circulation['book_id'])
                 ->set('available_quantity', 'available_quantity + 1', FALSE)
                 ->update('books');
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() !== FALSE;
    }

    /**
     * Get single circulation record
     */
    public function get_circulation_record($id) {
        return $this->db->where('id', $id)->get('circulation')->row_array();
    }

    /**
     * Get all circulation records with pagination
     */
    public function get_circulation($limit = 10, $offset = 0) {
        return $this->db->limit($limit, $offset)
                        ->select('c.*,b.title as book_title, m.first_name, m.last_name')
                        ->from('circulation c')
                        ->join('books b', 'c.book_id = b.id')
                        ->join('members m', 'c.member_id = m.id')
                        ->order_by('c.id', 'DESC')
                        ->get()
                        ->result_array();
    }

    /**
     * Count all active circulation
     */
    public function count_circulation() {
        return $this->db->where('status !=', 'returned')
                        ->count_all_results('circulation');
    }

    /**
     * Get overdue books
     */
    public function get_overdue_books() {
        return $this->db->where('return_date <', date('Y-m-d'))
                        ->where('status !=', 'returned')
                        ->get('circulation')
                        ->result_array();
    }

    /**
     * Get all books (for management page) - excludes archived
     */
    public function get_all_books() {
        return $this->db->where('archived', 0)
                        ->order_by('id', 'DESC')
                        ->get('books')
                        ->result_array();
    }

    /**
     * Get archived books
     */
    public function get_archived_books() {
        return $this->db->where('archived', 1)
                        ->order_by('updated_at', 'DESC')
                        ->get('books')
                        ->result_array();
    }

    /**
     * Get all members (for management page)
     */
    public function get_all_members() {
        return $this->db->where('is_active', 1)->order_by('id', 'DESC')->get('members')->result_array();
    }

    /**
     * Get all circulations (for management page)
     */
    public function get_all_circulations() {
        return $this->db->select('c.*, b.title as book_title, CONCAT(m.first_name, " ", m.last_name) as member_name')
                        ->from('circulation c')
                        ->join('books b', 'b.id = c.book_id', 'left')
                        ->join('members m', 'm.id = c.member_id', 'left')
                        ->where('c.archived', 0)
                        ->order_by('c.id', 'DESC')
                        ->get()
                        ->result_array();
    }

    // ========================================
    // USER APPROVAL MANAGEMENT
    // ========================================

    /**
     * Get pending (inactive) users awaiting approval
     */
    public function get_pending_users() {
        return $this->db->where('is_active', 0)
                        ->where('role', 'member')
                        ->order_by('created_at', 'DESC')
                        ->get('users')
                        ->result_array();
    }

    /**
     * Count pending users
     */
    public function count_pending_users() {
        return $this->db->where('is_active', 0)
                        ->where('role', 'member')
                        ->count_all_results('users');
    }

    /**
     * Get approved (active) users
     */
    public function get_approved_users() {
        return $this->db->where('is_active', 1)
                        ->where('role', 'member')
                        ->order_by('created_at', 'DESC')
                        ->get('users')
                        ->result_array();
    }

    /**
     * Approve a user (set is_active = 1)
     */
    public function approve_user($id) {
        return $this->db->where('id', $id)
                        ->update('users', ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Reject/delete a pending user
     */
    public function reject_user($id) {
        return $this->db->where('id', $id)
                        ->where('is_active', 0)
                        ->delete('users');
    }

    /**
     * Deactivate an approved user
     */
    public function deactivate_user($id) {
        return $this->db->where('id', $id)
                        ->where('role !=', 'admin')
                        ->update('users', ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // ========================================
    // MEMBER/USER QUERIES
    // ========================================

    /**
     * Get member by email
     */
    public function get_member_by_email($email) {
        return $this->db->where('email', $email)->get('members')->row_array();
    }

    /**
     * Get user by username
     */
    public function get_user_by_username($username) {
        return $this->db->where('username', $username)->get('users')->row_array();
    }

    /**
     * Check if username exists
     */
    public function username_exists($username) {
        $this->db->where('username', $username);
        return $this->db->count_all_results('users') > 0;
    }

    /**
     * Check if email exists
     */
    public function email_exists($email) {
        $this->db->where('email', $email);
        return $this->db->count_all_results('users') > 0;
    }

    /**
     * Get user's borrowed books count
     */
    public function get_user_borrowed_count($member_id) {
        $this->db->where('member_id', $member_id);
        $this->db->where('status !=', 'returned');
        return $this->db->count_all_results('circulation');
    }

    /**
     * Get user's overdue books count
     */
    public function get_user_overdue_count($member_id) {
        $this->db->where('member_id', $member_id);
        $this->db->where('due_date <', date('Y-m-d'));
        $this->db->where('status', 'borrowed');
        return $this->db->count_all_results('circulation');
    }

    /**
     * Get total available books quantity
     */
    public function get_total_available_books() {
        $query = $this->db->select_sum('available_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        return $result['available_quantity'] ? (int)$result['available_quantity'] : 0;
    }

    /**
     * Get total books quantity
     */
    public function get_total_books_quantity() {
        $query = $this->db->select_sum('total_quantity')
                          ->where('archived', 0)
                          ->get('books');
        $result = $query->row_array();
        return $result['total_quantity'] ? (int)$result['total_quantity'] : 0;
    }

    /**
     * Get user's borrowed books (not returned)
     */
    public function get_user_borrowed_books($member_id) {
        return $this->db
            ->select('c.*, b.title as book_title, b.author, b.id as book_id, b.isbn')
            ->from('circulation c')
            ->join('books b', 'c.book_id = b.id')
            ->where('c.member_id', $member_id)
            ->where('c.status !=', 'returned')
            ->order_by('c.borrow_date', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Get user's borrowing history with pagination
     */
    public function get_user_history($member_id, $limit = 10, $offset = 0) {
        return $this->db
            ->select('c.*, b.title as book_title, b.author, b.id as book_id')
            ->from('circulation c')
            ->join('books b', 'c.book_id = b.id')
            ->where('c.member_id', $member_id)
            ->order_by('c.borrow_date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result_array();
    }

    /**
     * Count user's total borrowing history
     */
    public function count_user_history($member_id) {
        return $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->count_all_results();
    }

    /**
     * Count user's returned books
     */
    public function count_user_returned($member_id) {
        return $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->where('c.status', 'returned')
            ->count_all_results();
    }

    /**
     * Count user's currently borrowed books
     */
    public function count_user_borrowed($member_id) {
        return $this->db
            ->from('circulation c')
            ->where('c.member_id', $member_id)
            ->where('c.status', 'borrowed')
            ->count_all_results();
    }

    /**
     * Check if user has already borrowed a book
     */
    public function has_borrowed_book($member_id, $book_id) {
        $this->db->where('member_id', $member_id);
        $this->db->where('book_id', $book_id);
        $this->db->where('status !=', 'returned');
        return $this->db->get('circulation')->row_array();
    }

    /**
     * Clear fine for a circulation record
     */
    public function clear_fine($circulation_id) {
        $data = array('fine_amount' => 0.00, 'updated_at' => date('Y-m-d H:i:s'));
        return $this->db->where('id', $circulation_id)->update('circulation', $data);
    }

    /**
     * Restore archived book
     */
    public function restore_book($id) {
        return $this->db->where('id', $id)
                        ->update('books', ['archived' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get inactive members
     */
    public function get_inactive_members() {
        return $this->db->where('is_active', 0)
                        ->order_by('updated_at', 'DESC')
                        ->get('members')
                        ->result_array();
    }

    /**
     * Activate member
     */
    public function activate_member($id) {
        return $this->db->where('id', $id)
                        ->update('members', ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get book by ISBN
     */
    public function get_book_by_isbn($isbn) {
        return $this->db->where('isbn', $isbn)->get('books')->row_array();
    }

    /**
     * Get member by email excluding specific member ID
     */
    public function get_member_by_email_excluding($email, $member_id) {
        $this->db->where('email', $email);
        $this->db->where('id !=', $member_id);
        return $this->db->get('members')->row_array();
    }

    /**
     * Count active borrows for a member
     */
    public function count_active_borrows($member_id) {
        $this->db->where('member_id', $member_id);
        $this->db->where('status !=', 'returned');
        return $this->db->count_all_results('circulation');
    }

    /**
     * Archive circulation record
     */
    public function archive_circulation($id) {
        return $this->db->where('id', $id)
                        ->update('circulation', ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get archived circulation records
     */
    public function get_archived_circulations() {
        return $this->db->select('c.*, b.title as book_title, CONCAT(m.first_name, " ", m.last_name) as member_name')
                        ->from('circulation c')
                        ->join('books b', 'b.id = c.book_id', 'left')
                        ->join('members m', 'm.id = c.member_id', 'left')
                        ->where('c.archived', 1)
                        ->order_by('c.updated_at', 'DESC')
                        ->get()
                        ->result_array();
    }

    /**
     * Restore archived circulation record
     */
    public function restore_circulation($id) {
        return $this->db->where('id', $id)
                        ->update('circulation', ['archived' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user by email
     */
    public function get_user_by_email($email) {
        return $this->db->where('email', $email)->get('users')->row_array();
    }

    // ========================================
    // PASSWORD RESET
    // ========================================

    /**
     * Generate password reset token
     */
    public function generate_reset_token($email) {
        $token = bin2hex(random_bytes(50));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->where('email', $email)->update('users', [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ]);
        
        return $token;
    }

    /**
     * Verify reset token
     */
    public function verify_reset_token($token) {
        $user = $this->db->where('password_reset_token', $token)
                         ->where('password_reset_expires >', date('Y-m-d H:i:s'))
                         ->get('users')
                         ->row_array();
        return $user;
    }

    /**
     * Reset user password
     */
    public function reset_password($token, $new_password) {
        $user = $this->verify_reset_token($token);
        
        if (!$user) {
            return false;
        }
        
        $this->db->where('id', $user['id'])->update('users', [
            'password' => password_hash($new_password, PASSWORD_BCRYPT),
            'password_reset_token' => NULL,
            'password_reset_expires' => NULL,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return $this->db->affected_rows() > 0;
    }
}
?>
