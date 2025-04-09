// validation.js

// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', () => {
    // Original navigation and scroll code remains the same
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Enhanced form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            let isValid = true;

            // Email validation
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value)) {
                    showError(emailInput, 'Please enter a valid email address');
                    isValid = false;
                }
            }

            // Password validation
            const passwordInput = form.querySelector('input[type="password"]');
            if (passwordInput) {
                const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
                if (!passwordPattern.test(passwordInput.value)) {
                    showError(passwordInput, 'Password must be at least 8 characters with letters and numbers');
                    isValid = false;
                }
            }

            // Payment validation
            const cardNumber = form.querySelector('input[name="cardNumber"]');
            if (cardNumber) {
                const cardPattern = /^\d{16}$/;
                if (!cardPattern.test(cardNumber.value.replace(/\s/g, ''))) {
                    showError(cardNumber, 'Please enter a valid 16-digit card number');
                    isValid = false;
                }
            }

            // Required fields validation
            const requiredInputs = form.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    showError(input, 'This field is required');
                    isValid = false;
                }
            });

            if (isValid) {
                form.submit();
            }
        });
    });

    function showError(element, message) {
        element.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        if (!element.nextElementSibling?.classList.contains('error-message')) {
            element.parentNode.insertBefore(errorDiv, element.nextElementSibling);
        }
    }

    // Original animation code remains the same
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        });

        animatedElements.forEach(element => {
            observer.observe(element);
        });
    } else {
        animatedElements.forEach(element => element.classList.add('visible'));
    }
});