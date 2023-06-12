DROP DATABASE IF EXISTS id18657476_gitpass;

CREATE DATABASE id18657476_gitpass;

USE id18657476_gitpass;

CREATE TABLE
    users (
        user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARBINARY(255) NOT NULL,
        salt VARBINARY(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (username),
        UNIQUE (email)
    );
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `salt`, `created_at`) VALUES
(1, 'chandan', 'chandan@gmail.com', 0xe79445eedd73f4bb9716e734ed226d9c, 0xffe7639938b888e5a13357a45a363fedc5a989793de17724, '2023-06-12 21:54:29');

CREATE TABLE
    social_accounts (
        account_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        site VARCHAR(255) NOT NULL,
        username VARCHAR(100) NOT NULL,
        password VARBINARY(255) NOT NULL,
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
        previous_password VARBINARY(255) NOT NULL,
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