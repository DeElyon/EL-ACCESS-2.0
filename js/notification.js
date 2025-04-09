class NotificationManager {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.apiEndpoint = '/api/notifications';
        this.initialize();
    }

    initialize() {
        // Create notification container
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        document.body.appendChild(this.container);
        
        // Start polling for new notifications
        this.startPolling();
    }

    async fetchNotifications() {
        try {
            const response = await fetch(this.apiEndpoint);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching notifications:', error);
            return [];
        }
    }

    displayNotification(notification) {
        const notifElement = document.createElement('div');
        notifElement.className = 'notification';
        notifElement.innerHTML = `
            <div class="notification-content">
                <h4>${notification.title}</h4>
                <p>${notification.message}</p>
                <span class="timestamp">${new Date(notification.timestamp).toLocaleString()}</span>
            </div>
            <button class="close-btn">&times;</button>
        `;

        // Add close button functionality
        notifElement.querySelector('.close-btn').addEventListener('click', () => {
            notifElement.remove();
            this.markAsRead(notification.id);
        });

        this.container.appendChild(notifElement);

        // Auto-remove after 5 seconds
        setTimeout(() => notifElement.remove(), 5000);
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`${this.apiEndpoint}/${notificationId}/read`, {
                method: 'POST'
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    startPolling() {
        // Check for new notifications every 30 seconds
        setInterval(async () => {
            const notifications = await this.fetchNotifications();
            notifications.forEach(notification => {
                if (!this.notifications.find(n => n.id === notification.id)) {
                    this.notifications.push(notification);
                    this.displayNotification(notification);
                }
            });
        }, 30000);
    }
}

// Export the notification manager
export const notificationManager = new NotificationManager();