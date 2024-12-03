-- Drop tables if they exist
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS reviews;

-- User Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(30) NOT NULL UNIQUE
);

-- Movie Table
CREATE TABLE IF NOT EXISTS movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    production VARCHAR(255) NOT NULL,
    genre VARCHAR(50),
    released_date DATE,
    image_path VARCHAR(255)
);

-- Review Table
CREATE TABLE IF NOT EXISTS reviews (
    user_id INTEGER,
    movie_id INTEGER,
    rating INTEGER,
    review_text TEXT,
    date DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Insert data into users table
INSERT INTO users (name) VALUES
('John Doe'),
('Jane Smith'),
('Michael Lee'),
('Alice Johnson'),
('David Miller'),
('Emily Davis'),
('Chris Evans'),
('Natalie Portman'),
('Tom Hanks'),
('Scarlett Johansson');

-- Insert data into movies table
INSERT INTO movies (name, production, genre, released_date, image_path) VALUES
('Titanic', 'Paramount Pictures', 'Drama, Romance', '1997-12-19', '1.jpg'),
('Top Gun', 'Paramount Pictures', 'Action, Drama', '1986-05-16', '2.webp'),
('Mission: Impossible - Dead Reckoning Part One', 'Paramount Pictures', 'Action, Adventure, Thriller', '2023-07-12', '3.jpg'),
('One Piece Film: Red', 'Toei Animation', 'Action, Adventure, Fantasy', '2022-08-06', '4.webp'),
('Spy x Family Code: White', 'Toho Animation', 'Action, Comedy, Family', '2023-12-22', '5.webp');


-- Insert data into reviews table
INSERT INTO reviews (user_id, movie_id, rating, review_text, date) VALUES
-- Titanic Reviews
(1, 1, 5, 'Titanic is a timeless romance. The emotional impact is unforgettable.', '2024-08-15'),
(2, 1, 4, 'A classic, but it drags a bit in the middle. Still a powerful movie.', '2024-08-12'),
(3, 1, 5, 'The love story and the historical context are perfectly balanced.', '2024-08-10'),
(4, 1, 5, 'One of the best movies I have ever seen.', '2024-08-18'),
(5, 1, 4, 'Great but the runtime was a bit too long.', '2024-08-17'),
(6, 1, 3, 'The visuals were stunning but the pacing was slow.', '2024-08-11'),
(7, 1, 5, 'Titanic will always be my favorite.', '2024-08-13'),
(8, 1, 4, 'The acting was superb, but some parts dragged on.', '2024-08-14'),
(9, 1, 5, 'Iconic movie with great performances.', '2024-08-19'),
(10, 1, 4, 'Amazing visuals, but a bit overrated.', '2024-08-20'),

-- Top Gun Reviews
(1, 2, 4, 'Top Gun is exhilarating! The aerial scenes are breathtaking, but the story is a bit dated.', '2024-08-16'),
(2, 2, 5, 'Great action sequences and an iconic soundtrack.', '2024-08-17'),
(3, 2, 4, 'The action was top-notch, but the characters could have been better.', '2024-08-13'),
(4, 2, 4, 'Loved the fighter jet scenes but the story felt weak.', '2024-08-12'),
(5, 2, 3, 'Fun, but a bit overrated for my taste.', '2024-08-11'),
(6, 2, 5, 'Classic 80s action at its best.', '2024-08-15'),
(7, 2, 4, 'Action-packed but a little cheesy at times.', '2024-08-10'),
(8, 2, 4, 'The soundtrack is amazing, but the plot is predictable.', '2024-08-19'),
(9, 2, 5, 'A nostalgic favorite that never gets old.', '2024-08-18'),
(10, 2, 4, 'Good movie, but some of the dialogue is cringy.', '2024-08-20'),

-- Mission: Impossible - Dead Reckoning Part One Reviews
(1, 3, 5, 'The latest Mission: Impossible is a non-stop thrill ride. One of the best in the series!', '2024-08-14'),
(2, 3, 4, 'Action-packed with incredible stunts, but the plot was somewhat convoluted.', '2024-08-11'),
(3, 3, 5, 'Tom Cruise never disappoints in this action-packed film.', '2024-08-12'),
(4, 3, 5, 'Amazing action, edge-of-the-seat stunts, loved every bit of it.', '2024-08-13'),
(5, 3, 5, 'This movie raised the bar for action films.', '2024-08-17'),
(6, 3, 4, 'Great stunts, but the story was a bit hard to follow.', '2024-08-16'),
(7, 3, 5, 'The best Mission: Impossible movie so far!', '2024-08-19'),
(8, 3, 5, 'A must-watch for action lovers.', '2024-08-15'),
(9, 3, 5, 'Incredible action scenes. Tom Cruise is unstoppable.', '2024-08-20'),
(10, 3, 4, 'Excellent movie but I expected a bit more from the story.', '2024-08-10'),

-- One Piece Film: Red Reviews
(1, 4, 5, 'One Piece Film: Red is an absolute treat for fans of the series. Fantastic visuals!', '2024-08-09'),
(2, 4, 5, 'Loved every second of this film. A must-watch for One Piece fans!', '2024-08-12'),
(3, 4, 5, 'The animation was stunning, and the plot was great too.', '2024-08-11'),
(4, 4, 4, 'Amazing film but felt a bit too long.', '2024-08-14'),
(5, 4, 5, 'Perfect movie for One Piece lovers, great visuals.', '2024-08-13'),
(6, 4, 5, 'It was everything I expected it to be. Excellent movie.', '2024-08-18'),
(7, 4, 4, 'Really good movie, but not the best One Piece film.', '2024-08-17'),
(8, 4, 5, 'The best One Piece movie ever made. I loved it!', '2024-08-19'),
(9, 4, 5, 'Beautifully animated and full of action.', '2024-08-16'),
(10, 4, 4, 'Great movie, but non-fans might get lost.', '2024-08-15'),

-- Spy x Family Code: White Reviews
(1, 5, 4, 'Spy x Family Code: White was full of laughs and action. A great family movie!', '2024-08-13'),
(2, 5, 5, 'Absolutely adorable movie, I loved it!', '2024-08-12'),
(3, 5, 4, 'The perfect mix of comedy and action. Highly recommend it.', '2024-08-10'),
(4, 5, 5, 'This movie is a delightful experience for the whole family.', '2024-08-11'),
(5, 5, 4, 'The humor was spot-on, but some parts felt a bit too long.', '2024-08-15'),
(6, 5, 5, 'A great watch, both hilarious and heartwarming.', '2024-08-14'),
(7, 5, 4, 'I enjoyed the action, but I wish there was more character development.', '2024-08-16'),
(8, 5, 5, "One of the best animated movies I've seen this year.", '2024-08-18'),
(9, 5, 4, 'Great movie for Spy x Family fans. Full of action and laughter.', '2024-08-17'),
(10, 5, 5, "I couldn't stop laughing! Great for all ages.", '2024-08-19');