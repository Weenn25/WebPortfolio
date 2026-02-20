<section class="page-animate">
    <h2>My Skills</h2>
    
    <p>Here's an overview of my technical skills and proficiency levels as I continue to grow as a developer.</p>

    <div class="tech-logos">
        <div class="logo-item" title="HTML5">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5" class="tech-icon">
            <p>HTML5</p>
        </div>
        <div class="logo-item" title="CSS3">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3" class="tech-icon">
            <p>CSS3</p>
        </div>
        <div class="logo-item" title="JavaScript">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript" class="tech-icon">
            <p>JavaScript</p>
        </div>
        <div class="logo-item" title="PHP">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" class="tech-icon">
            <p>PHP</p>
        </div>
        <div class="logo-item" title="Java">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="tech-icon">
            <p>Java</p>
        </div>
        <div class="logo-item" title="MySQL">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="MySQL" class="tech-icon">
            <p>MySQL</p>
        </div>
    </div>

    <h3>Frontend Development</h3>
    <div class="skill-item">
        <div class="skill-name">HTML5</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="60"></div>
        </div>
        <span class="skill-percent">60%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">CSS3</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="70"></div>
        </div>
        <span class="skill-percent">70%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">JavaScript</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="40"></div>
        </div>
        <span class="skill-percent">40%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">Responsive Design</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="80"></div>
        </div>
        <span class="skill-percent">80%</span>
    </div>

    <h3>Backend Development</h3>
    <div class="skill-item">
        <div class="skill-name">PHP</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="50"></div>
        </div>
        <span class="skill-percent">50%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">MongoDB</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="10"></div>
        </div>
        <span class="skill-percent">10%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">MySQL & SQL</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="50"></div>
        </div>
        <span class="skill-percent">50%</span>
    </div>

    <h3>Tools & Technologies</h3>
    <div class="skill-item">
        <div class="skill-name">Git & Version Control</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="40"></div>
        </div>
        <span class="skill-percent">40%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">Visual Studio Code</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="60"></div>
        </div>
        <span class="skill-percent">60%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">XAMPP & Apache</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="80"></div>
        </div>
        <span class="skill-percent">80%</span>
    </div>

    <h3>Soft Skills</h3>
    <div class="skill-item">
        <div class="skill-name">Problem Solving</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="60"></div>
        </div>
        <span class="skill-percent">60%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">Team Collaboration</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="80"></div>
        </div>
        <span class="skill-percent">80%</span>
    </div>

    <div class="skill-item">
        <div class="skill-name">Communication</div>
        <div class="skill-bar">
            <div class="skill-level" data-width="80"></div>
        </div>
        <span class="skill-percent">80%</span>
    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var bars = document.querySelectorAll('.skill-level[data-width]');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var bar = entry.target;
                bar.style.width = bar.getAttribute('data-width') + '%';
                observer.unobserve(bar);
            }
        });
    }, { threshold: 0.2 });

    bars.forEach(function(bar) {
        bar.style.width = '0%';
        observer.observe(bar);
    });
});
</script>
