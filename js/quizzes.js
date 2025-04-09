class QuizManager {
    constructor() {
        this.currentQuiz = null;
        this.currentQuestion = 0;
        this.score = 0;
        this.timer = null;
        this.timeLimit = 0;
    }

    startQuiz(quizData, timeLimit) {
        this.currentQuiz = quizData;
        this.currentQuestion = 0;
        this.score = 0;
        this.timeLimit = timeLimit;
        this.startTimer();
        this.displayQuestion();
    }

    startTimer() {
        let timeLeft = this.timeLimit;
        const timerDisplay = document.getElementById('timer');
        
        this.timer = setInterval(() => {
            timeLeft--;
            if (timerDisplay) {
                timerDisplay.textContent = `Time: ${timeLeft}s`;
            }
            if (timeLeft <= 0) {
                this.endQuiz();
            }
        }, 1000);
    }

    displayQuestion() {
        if (!this.currentQuiz || this.currentQuestion >= this.currentQuiz.length) {
            this.endQuiz();
            return;
        }

        const question = this.currentQuiz[this.currentQuestion];
        const questionContainer = document.getElementById('question-container');
        if (questionContainer) {
            questionContainer.innerHTML = `
                <h2>${question.text}</h2>
                <div class="options">
                    ${question.options.map((option, index) => `
                        <button onclick="quizManager.submitAnswer(${index})">${option}</button>
                    `).join('')}
                </div>
            `;
        }
    }

    submitAnswer(selectedIndex) {
        const currentQ = this.currentQuiz[this.currentQuestion];
        if (selectedIndex === currentQ.correctAnswer) {
            this.score++;
        }
        this.currentQuestion++;
        this.displayQuestion();
    }

    endQuiz() {
        clearInterval(this.timer);
        const container = document.getElementById('question-container');
        if (container) {
            container.innerHTML = `
                <h2>Quiz Complete!</h2>
                <p>Your score: ${this.score}/${this.currentQuiz.length}</p>
                <button onclick="location.reload()">Try Again</button>
            `;
        }
    }
}

// Initialize quiz manager
const quizManager = new QuizManager();

// Export for use in other modules
export { QuizManager, quizManager };