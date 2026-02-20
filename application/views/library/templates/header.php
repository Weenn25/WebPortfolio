<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Library Management System</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/library.css') ?>">
    <style>
        body {
            padding-top: 70px;
            margin: 0;
        }

        /* Sidebar Layout */
        .sidebar {
            position: fixed;
            left: 0;
            top: 70px;
            width: 70px;
            min-height: calc(100vh - 70px);
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5aa0 100%);
            color: white;
            padding: 0;
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar.expanded {
            width: 260px;
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-toggle i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .sidebar.expanded .sidebar-toggle i {
            transform: rotate(180deg);
        }

        /* Sidebar Content */
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        /* Animation only applied via JavaScript on first load */
        .sidebar.animated {
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-260px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 1rem;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60px;
            white-space: nowrap;
        }

        .sidebar.expanded .nav-link {
            justify-content: flex-start;
            padding: 1rem 1.5rem;
        }

        .sidebar .nav-link i {
            font-size: 1.3rem;
            margin-right: 0;
            transition: margin 0.3s ease;
        }

        .sidebar.expanded .nav-link i {
            margin-right: 0.8rem;
        }

        .sidebar .nav-link span {
            display: none;
            white-space: normal;
        }

        .sidebar.expanded .nav-link span {
            display: inline;
        }

        /* Animation only applied via JavaScript on first load */
        .sidebar .nav-link.animated {
            animation: fadeInUp 0.5s ease-out backwards;
        }

        @media (min-width: 992px) {
            .sidebar .nav-link.animated:nth-child(1) { animation-delay: 0.1s; }
            .sidebar .nav-link.animated:nth-child(2) { animation-delay: 0.2s; }
            .sidebar .nav-link.animated:nth-child(3) { animation-delay: 0.3s; }
            .sidebar .nav-link.animated:nth-child(4) { animation-delay: 0.4s; }
            .sidebar .nav-link.animated:nth-child(5) { animation-delay: 0.5s; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: rgba(255, 255, 255, 0.5);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.25);
            border-left-color: white;
            font-weight: 600;
        }

        .main-content {
            margin-left: 70px;
            min-height: calc(100vh - 70px);
            padding: 30px;
            background-color: #ecf0f1;
            transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding-top: 40px;
        }

        .main-content.expanded {
            margin-left: 260px;
        }

        /* Tooltip for collapsed state */
        .navbar-toggle-sidebar {
            cursor: pointer;
            margin-left: 10px;
            font-size: 1.3rem;
        }

        /* Mobile Responsiveness */
        @media (max-width: 991px) {
            body {
                padding-top: 70px;
            }

            .sidebar-toggle {
                display: none;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 70px;
                width: 260px;
                height: auto;
                max-height: 0;
                opacity: 0;
                overflow: hidden;
                padding: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-right: 1px solid rgba(255, 255, 255, 0.1);
                border-bottom: none;
                animation: none;
                z-index: 999;
            }

            .sidebar.expanded {
                width: 260px;
            }

            .sidebar.show {
                width: 260px;
                max-height: calc(100vh - 70px);
                opacity: 1;
                animation: slideInLeftMobile 0.4s ease-out;
            }

            .sidebar.show .sidebar-toggle i {
                transform: rotate(180deg);
            }

            @keyframes slideInLeftMobile {
                from {
                    transform: translateX(-100%);
                }
                to {
                    transform: translateX(0);
                }
            }

            .sidebar .nav-link {
                justify-content: flex-start;
                padding: 1rem 1.5rem;
            }

            .sidebar .nav-link span {
                display: inline;
            }

            .sidebar .nav-link i {
                margin-right: 0.8rem;
            }

            .main-content {
                margin-left: 0;
                padding: 15px 12px;
                padding-top: 30px;
            }

            .main-content.expanded {
                margin-left: 0;
            }
        }

        /* Extra small devices */
        @media (max-width: 576px) {
            .main-content {
                padding: 12px 10px;
                padding-top: 30px;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .sidebar {
                width: 100%;
            }

            .sidebar.show {
                width: 100%;
            }
        }

        .navbar-toggler {
            border: 2px solid #3498db !important;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        /* Ensure navbar always stays on top */
        .navbar.fixed-top {
            z-index: 1050 !important;
            position: fixed !important;
        }
    </style>
    <!-- iziToast CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
    <script>
        // Sidebar toggle functionality (desktop only)
        function toggleSidebar() {
            const isMobile = window.innerWidth <= 991;
            if (isMobile) return; // Don't toggle on mobile, use navbar-toggler instead
            
            const sidebar = document.getElementById('sidebarNav');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.querySelector('.sidebar-toggle i');
            
            sidebar.classList.toggle('expanded');
            mainContent.classList.toggle('expanded');
            
            // Rotate the toggle icon
            toggleIcon.style.transform = sidebar.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0deg)';
            toggleIcon.style.transition = 'transform 0.3s ease';
            
            // Save sidebar state to localStorage
            localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
        }

        // Apply sidebar animations only on first page load, not on navigation
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebarNav');
            const mainContent = document.querySelector('.main-content');
            const toggleIcon = document.querySelector('.sidebar-toggle i');
            const navLinks = sidebar ? sidebar.querySelectorAll('.nav-link') : [];
            const isMobile = window.innerWidth <= 991;
            
            // Restore sidebar state from localStorage (desktop only)
            if (!isMobile && localStorage.getItem('sidebarExpanded') === 'true') {
                sidebar.classList.add('expanded');
                mainContent.classList.add('expanded');
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(180deg)';
                }
            }
            
            // Check if animations have already been applied in this session
            if (!sessionStorage.getItem('sidebarAnimationsApplied')) {
                // Apply animations only on first load
                if (sidebar && !isMobile) {
                    sidebar.classList.add('animated');
                }
                navLinks.forEach(link => {
                    if (!isMobile) {
                        link.classList.add('animated');
                    }
                });
                
                // Mark animations as applied in this session
                sessionStorage.setItem('sidebarAnimationsApplied', 'true');
            }

            // Close sidebar when a nav link is clicked on mobile
            if (isMobile) {
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        const bsCollapse = new bootstrap.Collapse(sidebar, { toggle: false });
                        bsCollapse.hide();
                    });
                });
            }
        });
    </script>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 1030 !important;">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('library/dashboard') ?>">
                <i class="bi bi-book"></i> Library System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav" aria-controls="sidebarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            Welcome, <strong><?= $this->session->userdata('library_username') ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('library/profile') ?>">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('library/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebarNav">
        <!-- Sidebar Toggle Button -->
        <div class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="bi bi-chevron-right"></i>
        </div>

        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <ul class="nav flex-column">
                <?php 
                $role = $this->session->userdata('library_role');
                $is_admin = ($role === 'admin' || $role === 'librarian');
                ?>
                
                <li class="nav-item">
                    <a class="nav-link <?= ($page_title ?? '') === 'Dashboard' || ($page_title ?? '') === 'My Dashboard' ? 'active' : '' ?>" 
                       href="<?= $is_admin ? site_url('library/admin-dashboard') : site_url('library/user-dashboard') ?>"
                       title="Dashboard">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php if ($is_admin): ?>
                    <!-- Admin Menu -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'Books Management' ? 'active' : '' ?>" href="<?= site_url('library/books') ?>" title="Books">
                            <i class="bi bi-book"></i>
                            <span>Books</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'Members Management' ? 'active' : '' ?>" href="<?= site_url('library/members') ?>" title="Members">
                            <i class="bi bi-people"></i>
                            <span>Members</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'Circulation Management' ? 'active' : '' ?>" href="<?= site_url('library/circulation') ?>" title="Circulation">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Circulation</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'Browse Books' ? 'active' : '' ?>" href="<?= site_url('library/browse') ?>" title="Browse Books">
                            <i class="bi bi-search"></i>
                            <span>Browse Books</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'My Borrowed Books' ? 'active' : '' ?>" href="<?= site_url('library/my-books') ?>" title="My Books">
                            <i class="bi bi-bookmark"></i>
                            <span>My Books</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page_title ?? '') === 'Borrowing History' ? 'active' : '' ?>" href="<?= site_url('library/history') ?>" title="History">
                            <i class="bi bi-clock-history"></i>
                            <span>History</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
