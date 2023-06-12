DROP DATABASE IF EXISTS id18657476_gitpass;

CREATE DATABASE id18657476_gitpass;

USE id18657476_gitpass;

CREATE TABLE
    users (
        user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (username),
        UNIQUE (email)
    );

CREATE TABLE
    social_accounts (
        account_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        site VARCHAR(255) NOT NULL,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(300) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX (user_id)
    );

CREATE TABLE
    social_account_tags (
        account_id INT NOT NULL PRIMARY KEY,
        tag_name VARCHAR(100) NOT NULL,
        FOREIGN KEY (account_id) REFERENCES social_accounts(account_id) ON DELETE CASCADE
    );

CREATE TABLE
    social_account_metadata (
        metadata_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        account_id INT NOT NULL,
        favorite BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (account_id) REFERENCES social_accounts(account_id) ON DELETE CASCADE,
        INDEX (account_id)
    );

CREATE TABLE
    password_history (
        history_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        account_id INT NOT NULL,
        previous_username VARCHAR(100) NOT NULL,
        previous_password VARCHAR(255) NOT NULL,
        changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (account_id) REFERENCES social_accounts (account_id) ON DELETE CASCADE,
        INDEX (account_id)
    );

CREATE TABLE
    login_track (
        session_id INT NOT NULL PRIMARY KEY,
        user_id INT NOT NULL,
        login_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        logout_time TIMESTAMP NULL,
        duration INT DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
        UNIQUE (session_id),
        INDEX (user_id)
    );

INSERT INTO
    users (username, email, password)
VALUES (
        'johndoe',
        'johndoe@gmail.com',
        'password123'
    ), (
        'janedoe',
        'janedoe@yahoo.com',
        'qwerty456'
    ), (
        'bobsmith',
        'bobsmith@hotmail.com',
        'letmein789'
    );

INSERT INTO
    social_accounts (
        user_id,
        title,
        site,
        username,
        password
    )
VALUES (
        1,
        'Facebook',
        'https://www.facebook.com',
        'johndoe',
        'fbpass123'
    ), (
        1,
        'Twitter',
        'https://www.twitter.com',
        'johndoe',
        'twitterpass456'
    ), (
        2,
        'Instagram',
        'https://www.instagram.com',
        'janedoe',
        'instapass789'
    ), (
        2,
        'LinkedIn',
        'https://www.linkedin.com',
        'janedoe',
        'linkedinpass123'
    ), (
        3,
        'GitHub',
        'https://www.github.com',
        'bobsmith',
        'githubpass456'
    );

INSERT INTO
    social_account_metadata (account_id, favorite)
VALUES (1, true), (2, false), (3, true), (4, true), (5, false);

INSERT INTO
    password_history (
        account_id,
        previous_username,
        previous_password
    )
VALUES (1, 'johndoe', 'fbpass123'), (1, 'johndoe', 'newfbpass456'), (
        1,
        'johndoe',
        'newestfbpass789'
    ), (2, 'johndoe', 'twitterpass456'), (
        2,
        'johndoe',
        'newtwitterpass789'
    ), (3, 'janedoe', 'instapass789'), (
        3,
        'janedoe',
        'newinstapass123'
    ), (
        4,
        'janedoe',
        'linkedinpass123'
    ), (5, 'bobsmith', 'githubpass456');