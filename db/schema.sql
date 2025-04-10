-- Create tables
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
    course_id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
    notification_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quizzes (
    quiz_id SERIAL PRIMARY KEY,
    course_id INTEGER REFERENCES courses(course_id),
    title VARCHAR(100) NOT NULL,
    description TEXT,
    passing_score INTEGER DEFAULT 70
);

CREATE TABLE payments (
    payment_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    course_id INTEGER REFERENCES courses(course_id),
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_progress (
    progress_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    course_id INTEGER REFERENCES courses(course_id),
    quiz_id INTEGER REFERENCES quizzes(quiz_id),
    score INTEGER,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subscriptions (
    subscription_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id),
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NOT NULL,
    status VARCHAR(20) CHECK (status IN ('active', 'expired')) DEFAULT 'active'
);

-- Insert sample data
INSERT INTO users (username, email, password_hash) VALUES
('john_doe', 'john@example.com', 'hashed_password_1'),
('jane_smith', 'jane@example.com', 'hashed_password_2');

INSERT INTO courses (title, description, price) VALUES
('Python Basics', 'Introduction to Python programming', 49.99),
('Web Development', 'Learn HTML, CSS, and JavaScript', 79.99);

INSERT INTO quizzes (course_id, title, description) VALUES
(1, 'Python Fundamentals Quiz', 'Test your Python basics knowledge'),
(1, 'Python Advanced Quiz', 'Test your advanced Python skills'),
(2, 'HTML & CSS Quiz', 'Test your web development skills');

INSERT INTO payments (user_id, course_id, amount) VALUES
(1, 1, 49.99),
(2, 2, 79.99);

INSERT INTO user_progress (user_id, course_id, quiz_id, score) VALUES
(1, 1, 1, 85),
(2, 2, 3, 92);

INSERT INTO subscriptions (user_id, expiry_date) VALUES
(1, CURRENT_TIMESTAMP + INTERVAL '1 year'),
(2, CURRENT_TIMESTAMP + INTERVAL '6 months');

INSERT INTO notifications (user_id, message, type) VALUES
(1, 'Welcome to the course!', 'welcome'),
(2, 'New quiz available', 'quiz');