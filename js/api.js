// api.js

const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:3000';
const DEFAULT_HEADERS = {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
};

// Function to fetch course list from the API
async function fetchCourseList() {
    try {
        const response = await fetch(`${API_BASE_URL}/api/courses`, {
            method: 'GET',
            headers: DEFAULT_HEADERS
        });
        if (!response.ok) {
            throw new Error(`Error fetching courses: ${response.status} ${response.statusText}`);
// Function to fetch user progress from the API
async function fetchUserProgress(userId) {
    if (!userId) {
        throw new Error('User ID is required');
    }
    try {
        const response = await fetch(`${API_BASE_URL}/api/users/${userId}/progress`, {
            method: 'GET',
            headers: DEFAULT_HEADERS
        });
        if (!response.ok) {
            throw new Error(`Error fetching user progress: ${response.status} ${response.statusText}`);
        }
        const progress = await response.json();
        return progress;
    } catch (error) {
        console.error('Failed to fetch user progress:', error);
        throw error;
    }
}
            throw new Error(`Error fetching user progress: ${response.statusText}`);
        }
        const progress = await response.json();
        return progress;
    } catch (error) {
        console.error(error);
        return null;
    }
}

// Example usage
async function init() {
    try {
        const courses = await fetchCourseList();
        console.log('Courses:', courses);

        const userId = 1; // Replace with actual user ID
        const progress = await fetchUserProgress(userId);
        console.log('User Progress:', progress);

        const quizId = 101; // Replace with actual quiz ID
        const quizResults = await fetchQuizResults(userId, quizId);
        console.log('Quiz Results:', quizResults);
    } catch (error) {
        console.error('Initialization failed:', error);
    }
}

// Run the initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    try {
        init();
        document.getElementById('loading').style.display = 'block'; // Show loading state
    } catch (error) {
        console.error('Initialization error:', error);
        document.getElementById('error-message').textContent = 'Failed to load content. Please try again.';
    }
}

// Example usage
(async () => {
    const courses = await fetchCourseList();
    console.log('Courses:', courses);

    const userId = 1; // Replace with actual user ID
    const progress = await fetchUserProgress(userId);
    console.log('User Progress:', progress);

    const quizId = 101; // Replace with actual quiz ID
    const quizResults = await fetchQuizResults(userId, quizId);
    console.log('Quiz Results:', quizResults);
})();