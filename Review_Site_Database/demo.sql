
-- order users by date joined
Select user_id, username, email, date_joined FROM users ORDER BY date_joined ASC;
-- order reviews by score
Select * FROM reviews ORDER BY rating DESC;
-- sort entertainment by release date
Select * FROM entertainments ORDER BY release_date ASC;
-- sort entertainment by genre WIP
SELECT entertainments.title, genres.genre_name FROM entertainments JOIN entertainment_genres ON entertainments.entertainment_id = entertainment_genres.entertainment_id JOIN genres ON entertainment_genres.genre_id = genres.genre_id ORDER BY genres.genre_name ASC;
-- sort users by role
SELECT users.user_id, users.username, user_roles.user_role_name FROM users JOIN user_roles ON users.user_role_id = user_roles.user_role_id ORDER BY user_roles.user_role_id ASC;

UPDATE users SET username = 'AliceWonderland' WHERE user_id = 7;
DELETE FROM users WHERE user_id = 6;
SELECT * FROM users;


