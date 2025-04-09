// Admin Dashboard Script

// State management
let users = [];
let courses = [];

// User management functions
const filterUsers = (searchTerm) => {
    return users.filter(user => 
        user.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        user.email.toLowerCase().includes(searchTerm.toLowerCase())
    );
};

const updateUserStatus = (userId, newStatus) => {
    const userIndex = users.findIndex(user => user.id === userId);
    if (userIndex !== -1) {
        users[userIndex].status = newStatus;
        return true;
    }
    return false;
};

// Course management functions
const updateCourse = (courseId, updateData) => {
    const courseIndex = courses.findIndex(course => course.id === courseId);
    if (courseIndex !== -1) {
        courses[courseIndex] = { ...courses[courseIndex], ...updateData };
        return true;
    }
    return false;
};

const filterCourses = (criteria) => {
    return courses.filter(course => 
        course.title.toLowerCase().includes(criteria.toLowerCase()) ||
        course.category.toLowerCase().includes(criteria.toLowerCase())
    );
};

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    // Initialize admin dashboard
    const userSearchInput = document.getElementById('userSearch');
    const courseSearchInput = document.getElementById('courseSearch');

    userSearchInput?.addEventListener('input', (e) => {
        const filteredUsers = filterUsers(e.target.value);
        displayUsers(filteredUsers);
    });

    courseSearchInput?.addEventListener('input', (e) => {
        const filteredCourses = filterCourses(e.target.value);
        displayCourses(filteredCourses);
    });
});

// Display functions
const displayUsers = (userList) => {
    const userContainer = document.getElementById('userList');
    if (!userContainer) return;
    
    userContainer.innerHTML = userList.map(user => `
        <div class="user-card">
            <h3>${user.name}</h3>
            <p>${user.email}</p>
            <button onclick="updateUserStatus('${user.id}', 'active')">Activate</button>
            <button onclick="updateUserStatus('${user.id}', 'inactive')">Deactivate</button>
        </div>
    `).join('');
};

const displayCourses = (courseList) => {
    const courseContainer = document.getElementById('courseList');
    if (!courseContainer) return;

    courseContainer.innerHTML = courseList.map(course => `
        <div class="course-card">
            <h3>${course.title}</h3>
            <p>${course.category}</p>
            <button onclick="editCourse('${course.id}')">Edit</button>
        </div>
    `).join('');
};