// Library Management System - Enhanced JavaScript with iziToast

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Add loading state to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 3000);
            }
        });
    });

    // Highlight required fields
    highlightRequiredFields();
});

// ===== NOTIFICATION FUNCTIONS =====

/**
 * Show error notification with iziToast
 */
function showError(message, title = 'Error') {
    iziToast.error({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 5000,
        backgroundColor: '#dc3545',
        titleColor: '#fff',
        messageColor: '#fff',
        progressBar: true,
        progressBarColor: '#fff',
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight',
        icon: 'bi bi-exclamation-circle'
    });
}

/**
 * Show success notification with iziToast
 */
function showSuccess(message, title = 'Success') {
    iziToast.success({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 4000,
        backgroundColor: '#198754',
        titleColor: '#fff',
        messageColor: '#fff',
        progressBar: true,
        progressBarColor: '#fff',
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight',
        icon: 'bi bi-check-circle'
    });
}

/**
 * Show info notification with iziToast
 */
function showInfo(message, title = 'Info') {
    iziToast.info({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 4000,
        backgroundColor: '#0dcaf0',
        titleColor: '#fff',
        messageColor: '#fff',
        progressBar: true,
        progressBarColor: '#fff',
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight',
        icon: 'bi bi-info-circle'
    });
}

/**
 * Show warning notification with iziToast
 */
function showWarning(message, title = 'Warning') {
    iziToast.warning({
        title: title,
        message: message,
        position: 'topRight',
        timeout: 4000,
        backgroundColor: '#ffc107',
        titleColor: '#000',
        messageColor: '#000',
        progressBar: true,
        progressBarColor: '#000',
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight',
        icon: 'bi bi-exclamation-triangle'
    });
}

/**
 * Show custom toast notification
 */
function showCustomToast(title, message, type = 'info', timeout = 4000) {
    const colors = {
        'success': '#198754',
        'error': '#dc3545',
        'warning': '#ffc107',
        'info': '#0dcaf0',
        'primary': '#0d6efd'
    };

    const textColor = type === 'warning' ? '#000' : '#fff';

    iziToast.show({
        title: title,
        message: message,
        position: 'topRight',
        timeout: timeout,
        backgroundColor: colors[type] || '#0d6efd',
        titleColor: textColor,
        messageColor: textColor,
        progressBar: true,
        progressBarColor: textColor,
        transitionIn: 'fadeInLeft',
        transitionOut: 'fadeOutRight'
    });
}

// ===== FORM VALIDATION =====

/**
 * Validate email format
 */
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate phone number
 */
function validatePhone(phone) {
    const regex = /^[\d\s\-\+\(\)]{7,}$/;
    return regex.test(phone) && phone.length >= 7;
}

/**
 * Validate password strength
 */
function validatePasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };

    if (password.length >= 8) strength.score++;
    else strength.feedback.push('At least 8 characters');

    if (/[a-z]/.test(password)) strength.score++;
    else strength.feedback.push('Lowercase letters');

    if (/[A-Z]/.test(password)) strength.score++;
    else strength.feedback.push('Uppercase letters');

    if (/[0-9]/.test(password)) strength.score++;
    else strength.feedback.push('Numbers');

    if (/[!@#$%^&*]/.test(password)) strength.score++;
    else strength.feedback.push('Special characters');

    return strength;
}

/**
 * Highlight required form fields
 */
function highlightRequiredFields() {
    const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('invalid', function(e) {
            e.preventDefault();
            showError('Please fill in all required fields');
            this.focus();
            this.classList.add('is-invalid');
        });

        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
}

// ===== TABLE FUNCTIONS =====

/**
 * Filter table by input value
 */
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    const rows = table.getElementsByTagName('tr');

    input.addEventListener('keyup', debounce(function() {
        const filter = this.value.toUpperCase();
        let visibleCount = 0;

        for (let i = 1; i < rows.length; i++) {
            const text = rows[i].textContent || rows[i].innerText;
            const isVisible = text.toUpperCase().includes(filter);
            rows[i].style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        }

        if (visibleCount === 0) {
            showInfo('No results found');
        }
    }, 300));
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) {
        showError('Table not found');
        return;
    }

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(csvRow.join(','));
    });

    downloadCSV(csv.join('\n'), filename);
    showSuccess('Table exported to CSV successfully!');
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    const downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(csvFile);
    downloadLink.download = filename;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

/**
 * Print table with styling
 */
function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) {
        showError('Table not found');
        return;
    }

    const printWindow = window.open('', '', 'height=600,width=900');
    const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .print-header { margin-bottom: 30px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>Report</h2>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </div>
            ${table.outerHTML}
        </body>
        </html>
    `;
    printWindow.document.write(html);
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
        showSuccess('Print dialog opened!');
    }, 250);
}

// ===== UTILITY FUNCTIONS =====

/**
 * Format date string
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Format currency
 */
function formatCurrency(value, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(value);
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text, message = 'Copied to clipboard!') {
    navigator.clipboard.writeText(text).then(() => {
        showSuccess(message);
    }).catch(() => {
        showError('Failed to copy to clipboard');
    });
}

/**
 * Get URL parameter
 */
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

/**
 * Redirect with delay
 */
function redirectToUrl(url, delay = 1500) {
    setTimeout(() => {
        window.location.href = url;
    }, delay);
}

/**
 * Show confirmation dialog
 */
function showConfirmation(title, message, onConfirm, onCancel) {
    if (confirm(`${title}\n\n${message}`)) {
        if (typeof onConfirm === 'function') onConfirm();
    } else {
        if (typeof onCancel === 'function') onCancel();
    }
}

// ===== DATA MANIPULATION =====

/**
 * Parse JSON safely
 */
function parseJSON(jsonString, fallback = {}) {
    try {
        return JSON.parse(jsonString);
    } catch (e) {
        console.error('JSON parse error:', e);
        return fallback;
    }
}

/**
 * Sort array by property
 */
function sortBy(array, property, ascending = true) {
    return array.sort((a, b) => {
        if (ascending) {
            return a[property] > b[property] ? 1 : -1;
        } else {
            return a[property] < b[property] ? 1 : -1;
        }
    });
}

/**
 * Filter array by multiple conditions
 */
function filterArray(array, conditions) {
    return array.filter(item => {
        return Object.keys(conditions).every(key => {
            return item[key] === conditions[key];
        });
    });
}

// ===== ANIMATION HELPERS =====

/**
 * Fade in element
 */
function fadeIn(element, duration = 500) {
    element.style.opacity = '0';
    element.style.display = 'block';
    setTimeout(() => {
        element.style.transition = `opacity ${duration}ms ease`;
        element.style.opacity = '1';
    }, 10);
}

/**
 * Fade out element
 */
function fadeOut(element, duration = 500) {
    element.style.transition = `opacity ${duration}ms ease`;
    element.style.opacity = '0';
    setTimeout(() => {
        element.style.display = 'none';
    }, duration);
}

/**
 * Slide down element
 */
function slideDown(element, duration = 300) {
    element.style.maxHeight = '0';
    element.style.overflow = 'hidden';
    element.style.transition = `max-height ${duration}ms ease`;
    setTimeout(() => {
        element.style.maxHeight = element.scrollHeight + 'px';
    }, 10);
}

/**
 * Slide up element
 */
function slideUp(element, duration = 300) {
    element.style.maxHeight = element.scrollHeight + 'px';
    element.style.overflow = 'hidden';
    element.style.transition = `max-height ${duration}ms ease`;
    setTimeout(() => {
        element.style.maxHeight = '0';
    }, 10);
}

// ===== RESPONSIVE HELPERS =====

/**
 * Check if device is mobile
 */
function isMobile() {
    return window.innerWidth <= 768;
}

/**
 * Check if device is tablet
 */
function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024;
}

/**
 * Check if device is desktop
 */
function isDesktop() {
    return window.innerWidth > 1024;
}

// ===== STORAGE HELPERS =====

/**
 * Save to localStorage
 */
function saveToStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('Storage error:', e);
        return false;
    }
}

/**
 * Get from localStorage
 */
function getFromStorage(key, fallback = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : fallback;
    } catch (e) {
        console.error('Storage error:', e);
        return fallback;
    }
}

/**
 * Remove from localStorage
 */
function removeFromStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        console.error('Storage error:', e);
        return false;
    }
}

// ===== PERFORMANCE MONITORING =====

/**
 * Measure function execution time
 */
function measureTime(functionName, fn) {
    const start = performance.now();
    fn();
    const end = performance.now();
    console.log(`${functionName} took ${(end - start).toFixed(2)}ms`);
}

// ===== EXPORT FUNCTIONS FOR GLOBAL USE =====
window.showError = showError;
window.showSuccess = showSuccess;
window.showInfo = showInfo;
window.showWarning = showWarning;
window.filterTable = filterTable;
window.exportTableToCSV = exportTableToCSV;
window.printTable = printTable;
window.validateEmail = validateEmail;
window.validatePhone = validatePhone;
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.copyToClipboard = copyToClipboard;
window.redirectToUrl = redirectToUrl;
window.isMobile = isMobile;
window.isTablet = isTablet;
window.isDesktop = isDesktop;
