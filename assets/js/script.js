// Confirm Delete
function confirmDelete(id, namaKost) {
    if (confirm(`Apakah Anda yakin ingin menghapus data "${namaKost}"?\n\nData yang dihapus tidak dapat dikembalikan!`)) {
        window.location.href = `hapus_kost.php?id=${id}`;
    }
    return false;
}

// Search Function
function searchData() {
    const searchValue = document.getElementById('searchInput').value;
    if (searchValue.trim() !== '') {
        window.location.href = `index.php?search=${encodeURIComponent(searchValue)}`;
    }
}

// Enter key untuk search
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchData();
            }
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const numberInputs = form.querySelectorAll('input[type="number"]');
            let valid = true;

            numberInputs.forEach(input => {
                const min = parseFloat(input.getAttribute('min'));
                const max = parseFloat(input.getAttribute('max'));
                const value = parseFloat(input.value);

                if (min !== null && value < min) {
                    alert(`${input.previousElementSibling.textContent} tidak boleh kurang dari ${min}`);
                    valid = false;
                    input.focus();
                    return;
                }

                if (max !== null && value > max) {
                    alert(`${input.previousElementSibling.textContent} tidak boleh lebih dari ${max}`);
                    valid = false;
                    input.focus();
                    return;
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide success messages
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });

    // Format harga input dengan pemisah ribuan
    const hargaInputs = document.querySelectorAll('input[name="harga"]');
    hargaInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = parseInt(value);
            }
        });
    });

    // Highlight active table row on hover
    const tableRows = document.querySelectorAll('#dataTable tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Loading animation for buttons
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.disabled = true;
                this.innerHTML = '⏳ Memproses...';
            }
        });
    });
});

// Real-time form validation feedback
function validateNumberRange(input, min, max) {
    const value = parseFloat(input.value);
    const label = input.previousElementSibling;
    
    if (value < min || value > max) {
        input.style.borderColor = '#ef4444';
        label.style.color = '#ef4444';
    } else {
        input.style.borderColor = '#10b981';
        label.style.color = '#10b981';
    }
}

// Add event listeners for rating inputs
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="fasilitas"], input[name="keamanan"], input[name="kebersihan"]');
    ratingInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateNumberRange(this, 1, 10);
        });
    });
});

// Print functionality (opsional)
function printTable() {
    window.print();
}

// Export to CSV (opsional - simpel version)
function exportToCSV() {
    const table = document.getElementById('dataTable');
    let csv = [];
    
    // Header
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        if (th.textContent !== 'Aksi') {
            headers.push(th.textContent);
        }
    });
    csv.push(headers.join(','));
    
    // Rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach((td, index) => {
            if (index < headers.length) {
                row.push('"' + td.textContent.replace(/"/g, '""') + '"');
            }
        });
        csv.push(row.join(','));
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'data_kost_' + new Date().getTime() + '.csv';
    a.click();
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
