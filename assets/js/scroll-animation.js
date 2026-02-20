// Intersection Observer for scroll-triggered animations
document.addEventListener('DOMContentLoaded', function() {
    // Create observer for elements that should animate on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add animation class when element enters viewport
                entry.target.classList.add('scroll-animate-in');
                // Optional: stop observing after animation is triggered
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with scroll-animate class
    const animatedElements = document.querySelectorAll('.scroll-animate');
    animatedElements.forEach(el => {
        observer.observe(el);
    });

    // Also handle about-content h3 elements (section headings)
    const aboutHeadings = document.querySelectorAll('.about-content h3');
    aboutHeadings.forEach(heading => {
        heading.classList.add('scroll-animate');
        observer.observe(heading);
    });

    // Handle about-content paragraphs
    const aboutParagraphs = document.querySelectorAll('.about-content > p');
    aboutParagraphs.forEach(p => {
        p.classList.add('scroll-animate');
        observer.observe(p);
    });

    // Handle about-content lists
    const aboutLists = document.querySelectorAll('.about-content > ul');
    aboutLists.forEach(ul => {
        ul.classList.add('scroll-animate');
        observer.observe(ul);
    });

    // Also animate individual list items with stagger effect
    const aboutListItems = document.querySelectorAll('.about-content li');
    aboutListItems.forEach((li, index) => {
        li.classList.add('scroll-animate-item');
        li.style.setProperty('--item-index', index);
        const parentUl = li.closest('ul');
        if (parentUl) {
            observer.observe(li);
        }
    });
});
