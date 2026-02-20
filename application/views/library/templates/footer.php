    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/library-script.js') ?>"></script>
    <script>
        // Handle mobile sidebar menu
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarNav = document.getElementById('sidebarNav');
            const sidebarLinks = sidebarNav.querySelectorAll('.nav-link');
            
            // Close sidebar when a link is clicked on mobile
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        const toggleBtn = document.querySelector('.navbar-toggler');
                        toggleBtn.click(); // Toggle off the sidebar
                    }
                });
            });
            
            // Handle window resize for sidebar state
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebarNav.classList.add('show');
                } else {
                    sidebarNav.classList.remove('show');
                }
            });

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
