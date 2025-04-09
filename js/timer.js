class QuizTimer {
    constructor(durationInMinutes = 30) {
        this.totalSeconds = durationInMinutes * 60;
        this.remainingSeconds = this.totalSeconds;
        this.isRunning = false;
        this.timerInterval = null;
        this.callbacks = {
            onTick: null,
            onComplete: null
        };
    }

    start() {
        if (this.isRunning) return;
        this.isRunning = true;
        this.timerInterval = setInterval(() => this.tick(), 1000);
    }

    pause() {
        this.isRunning = false;
        clearInterval(this.timerInterval);
    }

    reset() {
        this.remainingSeconds = this.totalSeconds;
        this.pause();
        if (this.callbacks.onTick) {
            this.callbacks.onTick(this.getTimeString());
        }
    }

    tick() {
        if (this.remainingSeconds <= 0) {
            this.timeUp();
            return;
        }
        
        this.remainingSeconds--;
        if (this.callbacks.onTick) {
            this.callbacks.onTick(this.getTimeString());
        }
    }

    timeUp() {
        this.pause();
        alert('Time is up!');
        if (this.callbacks.onComplete) {
            this.callbacks.onComplete();
        }
    }

    getTimeString() {
        const minutes = Math.floor(this.remainingSeconds / 60);
        const seconds = this.remainingSeconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    onTick(callback) {
        this.callbacks.onTick = callback;
    }

    onComplete(callback) {
        this.callbacks.onComplete = callback;
    }
}

// Example usage:
// const timer = new QuizTimer(30); // 30 minutes
// timer.onTick((timeString) => {
//     document.getElementById('timer-display').textContent = timeString;
// });
// timer.onComplete(() => {
//     // Handle quiz submission
//     submitQuiz();
// });
class SubscriptionTimer extends QuizTimer {
    constructor(startDate = new Date()) {
        super();
        this.startDate = startDate;
        this.endDate = new Date(startDate.getTime() + (14 * 24 * 60 * 60 * 1000)); // 2 weeks in milliseconds
        this.hasAlerted = false;
    }
    setSubscriptionEndDate(date) {
        this.endDate = date;
        this.hasAlerted = false;
    }
    checkSubscription() {
        const now = new Date();
        const remaining = this.endDate - now;
        
        if (remaining <= 0) {
            this.timeUp();
            return;
        }

        const days = Math.floor(remaining / (24 * 60 * 60 * 1000));
        const hours = Math.floor((remaining % (24 * 60 * 60 * 1000)) / (60 * 60 * 1000));
        const minutes = Math.floor((remaining % (60 * 60 * 1000)) / (60 * 1000));

        if (days === 1 && !this.hasAlerted) {
            alert('Your subscription expires in 1 day!');
            this.hasAlerted = true;
        }

        return `${days}d ${hours}h ${minutes}m`;
    }

    startSubscriptionTracking() {
        setInterval(() => {
            const timeLeft = this.checkSubscription();
            if (this.callbacks.onTick) {
                this.callbacks.onTick(timeLeft);
            }
        }, 60000); // Update every minute
    }
}

// Usage example:
const subscriptionTimer = new SubscriptionTimer(new Date());
subscriptionTimer.onTick((timeString) => {
    document.getElementById('subscription-display').textContent = timeString;
});
subscriptionTimer.startSubscriptionTracking();