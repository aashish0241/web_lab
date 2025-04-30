document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 56, // Adjust for fixed navbar
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                }
            }
        });
    });
    
    // Form validation
    const contactForm = document.getElementById('contactForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    const formMessage = document.getElementById('formMessage');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Reset previous validation state
            let isValid = true;
            formMessage.innerHTML = '';
            
            // Remove previous validation classes
            nameInput.classList.remove('is-invalid');
            emailInput.classList.remove('is-invalid');
            messageInput.classList.remove('is-invalid');
            
            // Validate name (required)
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate email (required and format)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate message (required and min length)
            if (!messageInput.value.trim() || messageInput.value.trim().length < 10) {
                messageInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
                formMessage.innerHTML = '<div class="alert alert-danger">Please correct the errors in the form.</div>';
                return false;
            }
            
            // If using AJAX submission (optional)
            /*
            e.preventDefault();
            
            // Create form data object
            const formData = new FormData(contactForm);
            
            // Send AJAX request
            fetch('process_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formMessage.innerHTML = '<div class="alert alert-success">Thank you! Your message has been sent successfully.</div>';
                    contactForm.reset();
                } else {
                    formMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                formMessage.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
                console.error('Error:', error);
            });
            */
        });
    }
    
    // Animate skill bars when they come into view
    const skillLevels = document.querySelectorAll('.skill-level');
    
    // Check if IntersectionObserver is supported
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Add width animation when in viewport
                    entry.target.style.width = entry.target.textContent;
                    // Unobserve after animation
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        skillLevels.forEach(skill => {
            // Start with 0 width
            skill.style.width = '0%';
            // Observe each skill
            observer.observe(skill);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        skillLevels.forEach(skill => {
            skill.style.width = skill.textContent;
        });
    }
});
