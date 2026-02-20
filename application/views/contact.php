<section>
    <h2>Get In Touch</h2>
    <p>Feel free to reach out to me through any of these channels. I'd love to connect and discuss opportunities!</p>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="notice success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <div class="contact-grid">
        <div class="contact-info">
            <h3>Contact Information</h3>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <p><strong>Email</strong></p>
                    <a href="mailto:erambonanzaa@gmail.com">erambonanzaa@gmail.com</a>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                    <p><strong>Phone</strong></p>
                    <a href="tel:+639920586149">+639920586149</a>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <p><strong>Location</strong></p>
                    <p>Dasmariñas City Cavite, Philippines</p>
                </div>
            </div>
        </div>

        <div class="social-section">
            <h3>Connect With Me</h3>
            <div class="social-list">
                <a href="https://www.linkedin.com/in/reween-rambonanza-907b293a8/" target="_blank" title="LinkedIn" class="social-item">
                    <i class="fab fa-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
                <a href="https://github.com/Weenn25" target="_blank" title="GitHub" class="social-item">
                    <i class="fab fa-github"></i>
                    <span>GitHub</span>
                </a>
                <a href="https://www.facebook.com/reween.rambonanza" target="_blank" title="Facebook" class="social-item">
                    <i class="fab fa-facebook"></i>
                    <span>Facebook</span>
                </a>
                <a href="https://www.instagram.com/weennnnnn1/" target="_blank" title="Instagram" class="social-item">
                    <i class="fab fa-instagram"></i>
                    <span>Instagram</span>
                </a>
            </div>
        </div>
    </div>
</section>