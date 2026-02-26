    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/library-script.js') ?>"></script>
    <script>
        // Get tab ID from sessionStorage
        function getTabId() {
            let tabId = sessionStorage.getItem('currentTabId');
            if (!tabId) {
                tabId = sessionStorage.getItem('tabId');
            }
            return tabId || '';
        }

        // Intercept form submissions to add tab_id
        function interceptFormSubmissions() {
            const tabId = getTabId();
            
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Check if form already has tab_id field
                    let hasTabId = false;
                    this.querySelectorAll('input[name="tab_id"]').forEach(() => {
                        hasTabId = true;
                    });
                    
                    if (!hasTabId && tabId) {
                        // Add hidden input for tab_id
                        const tabInput = document.createElement('input');
                        tabInput.type = 'hidden';
                        tabInput.name = 'tab_id';
                        tabInput.value = tabId;
                        this.appendChild(tabInput);
                    }
                });
            });
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            interceptFormSubmissions();
        });

        // Also run after small delay to catch dynamically added forms
        setTimeout(function() {
            interceptFormSubmissions();
        }, 500);

        // Handle sidebar toggle functionality (desktop only)
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

            // iziToast Notifications for Flash Messages
            setTimeout(function() {
                if (typeof iziToast !== 'undefined') {
                    <?php if($this->session->flashdata('success')): ?>
                        iziToast.success({
                            title: 'Success',
                            message: '<?= addslashes($this->session->flashdata('success')) ?>',
                            position: 'topRight',
                            timeout: 4000
                        });
                    <?php endif; ?>

                    <?php if($this->session->flashdata('error')): ?>
                        iziToast.error({
                            title: 'Error',
                            message: '<?= addslashes($this->session->flashdata('error')) ?>',
                            position: 'topRight',
                            timeout: 4000
                        });
                    <?php endif; ?>

                    <?php if($this->session->flashdata('warning')): ?>
                        iziToast.warning({
                            title: 'Warning',
                            message: '<?= addslashes($this->session->flashdata('warning')) ?>',
                            position: 'topRight',
                            timeout: 4000
                        });
                    <?php endif; ?>

                    <?php if($this->session->flashdata('info')): ?>
                        iziToast.info({
                            title: 'Info',
                            message: '<?= addslashes($this->session->flashdata('info')) ?>',
                            position: 'topRight',
                            timeout: 4000
                        });
                    <?php endif; ?>
                }
            }, 100);
        });
    </script>
</body>
</html>
