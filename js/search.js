// Search functionality
const searchInput = document.querySelector('#search-input');
const searchResults = document.querySelector('#search-results');

// Data structure to hold searchable content (to be populated from your database/API)
let searchableContent = {
    courses: [],    // [{id: 1, title: 'Course 1', description: '...'}]
    lessons: [],    // [{id: 1, title: 'Lesson 1', description: '...'}]
    faqs: []        // [{id: 1, question: 'FAQ 1', answer: '...'}]
};

// Debounce function to limit API calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Search function
function performSearch(query) {
    if (!query) {
        searchResults.innerHTML = '';
        return;
    }

    query = query.toLowerCase();
    
    // Filter through all content
    const results = {
        courses: searchableContent.courses.filter(course => 
            course.title.toLowerCase().includes(query) || 
            course.description.toLowerCase().includes(query)
        ),
        lessons: searchableContent.lessons.filter(lesson =>
            lesson.title.toLowerCase().includes(query) ||
            lesson.description.toLowerCase().includes(query)
        ),
        faqs: searchableContent.faqs.filter(faq =>
            faq.question.toLowerCase().includes(query) ||
            faq.answer.toLowerCase().includes(query)
        )
    };

    displayResults(results);
}

// Display results in DOM
function displayResults(results) {
    searchResults.innerHTML = '';
    
    const resultsHTML = [];

    if (results.courses.length) {
        resultsHTML.push('<h3>Courses</h3>');
        results.courses.forEach(course => {
            resultsHTML.push(`
                <div class="search-result course">
                    <h4>${course.title}</h4>
                    <p>${course.description}</p>
                </div>
            `);
        });
    }

    if (results.lessons.length) {
        resultsHTML.push('<h3>Lessons</h3>');
        results.lessons.forEach(lesson => {
            resultsHTML.push(`
                <div class="search-result lesson">
                    <h4>${lesson.title}</h4>
                    <p>${lesson.description}</p>
                </div>
            `);
        });
    }

    if (results.faqs.length) {
        resultsHTML.push('<h3>FAQs</h3>');
        results.faqs.forEach(faq => {
            resultsHTML.push(`
                <div class="search-result faq">
                    <h4>${faq.question}</h4>
                    <p>${faq.answer}</p>
                </div>
            `);
        });
    }

    if (!resultsHTML.length) {
        searchResults.innerHTML = '<p>No results found</p>';
    } else {
        searchResults.innerHTML = resultsHTML.join('');
    }
}

// Initialize search functionality
function initializeSearch() {
    // Attach event listener with debounce
    searchInput.addEventListener('input', debounce((e) => {
        performSearch(e.target.value.trim());
    }, 300));

    // Fetch initial data (replace with your actual data fetching logic)
    fetch('/api/searchable-content')
        .then(response => response.json())
        .then(data => {
            searchableContent = data;
        })
        .catch(error => {
            console.error('Error fetching searchable content:', error);
        });
}

// Start the search functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeSearch);