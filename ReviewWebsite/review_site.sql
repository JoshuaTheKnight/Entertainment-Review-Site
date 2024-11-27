-- create database
DROP DATABASE IF EXISTS entertainment_reviews;
CREATE DATABASE entertainment_reviews;
USE entertainment_reviews;

-- create user table
CREATE TABLE users (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(50) UNIQUE,
    user_password VARCHAR(255),
    date_joined DATE,
    user_role_id INT
    
);

CREATE TABLE user_roles(
	user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    -- 1 = owner, 2 = admin, 3 = regular user
    user_role_name VARCHAR(50)
);

CREATE TABLE entertainments(
	entertainment_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50),
    release_date DATE,
    entertainment_description MEDIUMTEXT,
    entertainment_type_id INT
);

CREATE TABLE entertainment_types(
	entertainment_type_id INT AUTO_INCREMENT PRIMARY KEY,
    entertainment_name VARCHAR(50)
);

CREATE TABLE reviews(
	review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    entertainment_id INT,
    rating INT,
    review_text MEDIUMTEXT,
    review_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (entertainment_id) REFERENCES entertainments(entertainment_id) ON DELETE CASCADE
);

CREATE TABLE comments(
	comment_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT,
    user_id INT,
    comment_text MEDIUMTEXT,
    comment_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (review_id) REFERENCES reviews(review_id) ON DELETE CASCADE
);

CREATE TABLE genres(
	genre_id INT AUTO_INCREMENT PRIMARY KEY,
    genre_name VARCHAR(50)
);

CREATE TABLE entertainment_genres(
	entertainment_id INT,
    genre_id INT
);

INSERT INTO users (username, email, user_password, date_joined, user_role_id) VALUES
('AlwaysCorrectOwner', 'TheBest@gmail.com', 'dontworryaboutit1337', '2024-10-6', 1),
('NotAsCorrectAdmin1', 'supercoolemail@outlook.com', 'wifesname(supersecure)', '2024-10-6', 2),
('NotAsCorrectAdmin2', 'superepicemail@outlook.com', 'husbandsname(supersecure)', '2024-10-6', 2),
('SuperIncorrectUser', 'LacksMediaLiteracy@gmail.com', 'IEatPokemonCardsForTheFlavor', '2024-10-7', 3),
('john_doe', 'john@example.com', 'password123', '2023-01-15', 3),
('jane_smith', 'jane@example.com', 'password456', '2023-02-20', 3),
('alice_jones', 'alice@example.com', 'password789', '2023-03-10', 3);

INSERT INTO user_roles (user_role_name) VALUES
('Owner'),
('Admin'),
('Standard');

INSERT INTO entertainments (title, release_date, entertainment_description, entertainment_type_id) VALUES
('Inception', '2010-07-16', 'A mind-bending thriller by Christopher Nolan.', 1),
('Breaking Bad', '2008-01-20', 'A high school chemistry teacher turned methamphetamine producer.', 2),
('The Beatles: Abbey Road', '1969-09-26', 'The Beatles’ iconic album.', 3),
('The Matrix', '1999-03-31', 'A computer hacker learns about the true nature of his reality and his role in the war against its controllers.', 1),
('Friends', '1994-09-22', 'Follows the personal and professional lives of six twenty to thirty-something-year-old friends living in Manhattan.', 2);

INSERT INTO entertainment_types (entertainment_name) VALUES
('Movie'),
('TV Show'),
('Music');

INSERT INTO reviews (user_id, entertainment_id, rating, review_text, review_date) VALUES
(1, 1, 8, 'Amazing movie! A must-watch.', '2023-04-01'),
(2, 2, 7, 'Great show, but the ending could be better.', '2023-04-05'),
(3, 3, 6, 'It was okay, not my favorite.', '2023-04-10'),
(4, 4, 9, 'Pretty gas ngl, the special effects were cool and the story was pretty sick. Would watch again', '2024-05-08');

INSERT INTO comments (review_id, user_id, comment_text, comment_date) VALUES
(1, 2, 'I totally agree with you!', '2023-04-02'),
(2, 3, 'I thought the ending was perfect!', '2023-04-06'),
(3, 1, 'I had the same feeling.', '2023-04-11');

INSERT INTO genres (genre_name) VALUES
('Science Fiction'),
('Drama'),
('Pop'),
('Action'),
('Comedy');

INSERT INTO entertainment_genres (entertainment_id, genre_id) VALUES
-- Inception
(1, 1), -- Science Fiction
(1, 4), -- Action
-- Breaking Bad
(2, 2), -- Drama
-- The Beatles: Abbey Road
(3, 3), -- Pop
-- The Matrix
(4, 1), -- Science Fiction
(4, 4), -- Action
-- Friends
(5, 2), -- Drama
(5, 5); -- Comedy