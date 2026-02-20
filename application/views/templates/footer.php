</main>
<footer class="site-footer">
    <div class="wrap">
        <p>&copy; <?= date('Y') ?> <?= isset($site_title) ? $site_title : 'My Portfolio' ?>. Built with CodeIgniter 3.</p>
    </div>
</footer>

<script>
// Hamburger Menu Toggle
document.addEventListener('DOMContentLoaded', function(){
    const hamburger = document.getElementById('hamburger');
    const mainNav = document.getElementById('mainNav');

    if(!hamburger || !mainNav) return;

    // ensure initial accessibility state
    hamburger.setAttribute('aria-expanded', 'false');
    mainNav.setAttribute('aria-hidden', mainNav.classList.contains('active') ? 'false' : 'true');

    function setActiveState(active){
        if(active){
            mainNav.classList.add('active');
            hamburger.classList.add('active');
            hamburger.setAttribute('aria-expanded', 'true');
            mainNav.setAttribute('aria-hidden', 'false');
        } else {
            mainNav.classList.remove('active');
            hamburger.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
            mainNav.setAttribute('aria-hidden', 'true');
        }
    }

    hamburger.addEventListener('click', function(){
        const isActive = mainNav.classList.toggle('active');
        hamburger.classList.toggle('active');
        hamburger.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        mainNav.setAttribute('aria-hidden', isActive ? 'false' : 'true');
    });

    // Close menu when a link is clicked
    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(){
            setActiveState(false);
        });
    });

    // close on escape key
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') setActiveState(false);
    });
});

// Typewriter Animation
document.addEventListener('DOMContentLoaded', function(){
    const titleEl = document.querySelector('.hero-title');
    if(!titleEl) return;
    
    const fullText = "Hi, I'm Reween!";
    const accentText = "Reween";
    const typingSpeed = 100;
    const deleteSpeed = 50;
    const delayBeforeDelete = 2000;
    
    function typeWriter(){
        let index = 0;
        titleEl.innerHTML = '';
        
        function type(){
            if(index < fullText.length){
                const char = fullText[index];
                const beforeAccent = fullText.substring(0, fullText.indexOf(accentText));
                const afterAccent = fullText.substring(fullText.indexOf(accentText) + accentText.length);
                
                if(index < beforeAccent.length){
                    titleEl.textContent = fullText.substring(0, index + 1);
                } else if(index < beforeAccent.length + accentText.length){
                    titleEl.innerHTML = beforeAccent + '<span class="accent">' + accentText.substring(0, index - beforeAccent.length + 1) + '</span>';
                } else {
                    titleEl.innerHTML = beforeAccent + '<span class="accent">' + accentText + '</span>' + afterAccent.substring(0, index - beforeAccent.length - accentText.length + 1);
                }
                index++;
                setTimeout(type, typingSpeed);
            } else {
                setTimeout(deleteWriter, delayBeforeDelete);
            }
        } 
        type();
    }
    
    function deleteWriter(){
        let index = fullText.length;
        
        function deleteChar(){
            if(index > 0){
                const beforeAccent = fullText.substring(0, fullText.indexOf(accentText));
                const accentText2 = "Reween";
                const afterAccent = fullText.substring(fullText.indexOf(accentText2) + accentText2.length);
                
                if(index <= beforeAccent.length){
                    titleEl.textContent = fullText.substring(0, index - 1);
                } else if(index <= beforeAccent.length + accentText2.length){
                    titleEl.innerHTML = beforeAccent + '<span class="accent">' + accentText2.substring(0, index - beforeAccent.length - 1) + '</span>';
                } else {
                    titleEl.innerHTML = beforeAccent + '<span class="accent">' + accentText2 + '</span>' + afterAccent.substring(0, index - beforeAccent.length - accentText2.length - 1);
                }
                index--;
                setTimeout(deleteChar, deleteSpeed);
            } else {
                setTimeout(typeWriter, 500);
            }
        }
        deleteChar();
    }
    
    typeWriter();
});
</script>

</body>
</html>