// Dashboard functionality for el-access-2.0
document.addEventListener('DOMContentLoaded', () => {
    // Main dashboard state
    let userData = {
        subscriptionEndDate: null,
        progress: 0,
        notifications: []
    };

    // Fetch user data from server
    async function fetchUserData() {
        try {
            const response = await fetch('/api/user/data');
            userData = await response.json();
            updateDashboard();
        } catch (error) {
            console.error('Error fetching user data:', error);
        }
    }

    // Update subscription timer
    function updateSubscriptionTimer() {
        if (!userData.subscriptionEndDate) return;

        const endDate = new Date(userData.subscriptionEndDate);
        const now = new Date();
        const timeLeft = endDate - now;
        const daysLeft = Math.ceil(timeLeft / (1000 * 60 * 60 * 24));

        const timerElement = document.getElementById('subscription-timer');
        if (timerElement) {
            timerElement.textContent = `${daysLeft} days remaining`;
            if (daysLeft <= 3) {
                timerElement.classList.add('warning');
            }
        }
    }

    // Update progress indicators
    function updateProgress() {
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = `${userData.progress}%`;
            progressBar.setAttribute('aria-valuenow', userData.progress);
        }
    }

    // Handle notifications
    function displayNotifications() {
        const notificationList = document.getElementById('notification-list');
        if (notificationList && userData.notifications.length) {
            notificationList.innerHTML = userData.notifications
                .map(notif => `
                    <div class="notification">
                        <p>${notif.message}</p>
                        <span class="date">${new Date(notif.date).toLocaleDateString()}</span>
                    </div>
                `).join('');
        }
    }

    // Update all dashboard elements
    function updateDashboard() {
        updateSubscriptionTimer();
        updateProgress();
        displayNotifications();
    }

    // Initial setup
    fetchUserData();

    // Refresh data periodically
    setInterval(fetchUserData, 300000); // Update every 5 minutes

    // Event listeners for interactive elements
    document.getElementById('refresh-btn')?.addEventListener('click', fetchUserData);
});