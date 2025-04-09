// Admin Dashboard Scripts

// Table sorting utility
function sortTable(table, column, asc = true) {
    const dirModifier = asc ? 1 : -1;
    const rows = Array.from(table.querySelectorAll('tr'));
    const sortedRows = rows.sort((a, b) => {
        const aColText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
        const bColText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
        return aColText > bColText ? (1 * dirModifier) : (-1 * dirModifier);
    });

    table.tBodies[0].append(...sortedRows);
}

// Search/Filter functionality
function filterTable(input, tableId) {
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');

    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let cell of cells) {
            if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

// User Management
class UserManager {
    static async getUsers() {
        try {
            const response = await fetch('/api/users');
            return await response.json();
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    }

    static async updateUser(userId, userData) {
        try {
            const response = await fetch(`/api/users/${userId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
            return await response.json();
        } catch (error) {
            console.error('Error updating user:', error);
        }
    }
}

// Course Management
class CourseManager {
    static async getCourses() {
        try {
            const response = await fetch('/api/courses');
            return await response.json();
        } catch (error) {
            console.error('Error fetching courses:', error);
        }
    }

    static async createCourse(courseData) {
        try {
            const response = await fetch('/api/courses', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(courseData)
            });
            return await response.json();
        } catch (error) {
            console.error('Error creating course:', error);
        }
    }
}

// Payment Management
class PaymentManager {
    static async getPayments() {
        try {
            const response = await fetch('/api/payments');
            return await response.json();
        } catch (error) {
            console.error('Error fetching payments:', error);
        }
    }

    static async processRefund(paymentId) {
        try {
            const response = await fetch(`/api/payments/${paymentId}/refund`, {
                method: 'POST'
            });
            return await response.json();
        } catch (error) {
            console.error('Error processing refund:', error);
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Initialize sorting listeners
    const tables = document.querySelectorAll('table[data-sortable="true"]');
    tables.forEach(table => {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                const isAscending = header.getAttribute('data-sort-direction') === 'asc';
                sortTable(table, index, !isAscending);
                header.setAttribute('data-sort-direction', isAscending ? 'desc' : 'asc');
            });
        });
    });

    // Initialize search inputs
    const searchInputs = document.querySelectorAll('input[data-search-table]');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', () => {
            filterTable(input, input.getAttribute('data-search-table'));
        });
    });
});