<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Portfolio routes (existing)
$route['default_controller'] = 'portfolio';
$route['portfolio'] = 'portfolio/index';
$route['portfolio/home'] = 'portfolio/index';
$route['portfolio/about'] = 'portfolio/about';
$route['portfolio/skills'] = 'portfolio/skills';
$route['portfolio/projects'] = 'portfolio/projects';
$route['portfolio/contact'] = 'portfolio/contact';
$route['portfolio/sample'] = 'portfolio/sample';

// Library Management System routes (NEW)
$route['library'] = 'library/dashboard';
$route['library/dashboard'] = 'library/dashboard';
$route['library/admin-dashboard'] = 'library/admin_dashboard';
$route['library/user-dashboard'] = 'library/user_dashboard';
$route['library/login'] = 'library/login';
$route['library/register'] = 'library/register';
$route['library/forgot-password'] = 'library/forgot_password';
$route['library/reset-password/(:any)'] = 'library/reset_password/$1';
$route['library/logout'] = 'library/logout';
$route['library/profile'] = 'library/profile';
$route['library/edit-profile'] = 'library/edit_profile';
$route['library/change-password'] = 'library/change_password';
$route['library/change-password-ajax'] = 'library/change_password_ajax';
$route['library/books'] = 'library/books';
$route['library/books/add'] = 'library/add_book';
$route['library/books/insert'] = 'library/insert_book';
$route['library/books/archived'] = 'library/archived_books';
$route['library/books/view/(:num)'] = 'library/view_book/$1';
$route['library/books/edit/(:num)'] = 'library/edit_book/$1';
$route['library/books/update/(:num)'] = 'library/update_book/$1';
$route['library/books/archive/(:num)'] = 'library/archive_book/$1';
$route['library/books/restore/(:num)'] = 'library/restore_book/$1';
$route['library/members'] = 'library/members';
$route['library/members/add'] = 'library/add_member';
$route['library/members/insert'] = 'library/insert_member';
$route['library/members/inactive'] = 'library/inactive_members';
$route['library/members/view/(:num)'] = 'library/view_member/$1';
$route['library/members/edit/(:num)'] = 'library/edit_member/$1';
$route['library/members/update/(:num)'] = 'library/update_member/$1';
$route['library/members/deactivate/(:num)'] = 'library/deactivate_member/$1';
$route['library/members/activate/(:num)'] = 'library/activate_member/$1';
$route['library/browse'] = 'library/browse_books';
$route['library/browse/view/(:num)'] = 'library/browse_book_details/$1';
$route['library/get_book_details/(:num)'] = 'library/get_book_details/$1';
$route['library/my-books'] = 'library/my_books';
$route['library/history'] = 'library/history';
$route['library/borrow/(:num)'] = 'library/borrow_book/$1';
$route['library/return/(:num)'] = 'library/return_book/$1';
$route['library/clear-fine/(:num)'] = 'library/clear_fine/$1';
$route['library/circulation'] = 'library/circulation';
$route['library/circulation/archived'] = 'library/archived_circulations';
$route['library/circulation/archive/(:num)'] = 'library/archive_circulation/$1';
$route['library/circulation/restore/(:num)'] = 'library/restore_circulation/$1';
$route['library/pending-users'] = 'library/pending_users';
$route['library/approved-users'] = 'library/approved_users';
$route['library/approve-user/(:num)'] = 'library/approve_user/$1';
$route['library/reject-user/(:num)'] = 'library/reject_user/$1';
$route['library/deactivate-user/(:num)'] = 'library/deactivate_user/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;



