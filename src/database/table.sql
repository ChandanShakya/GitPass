CREATE DATABASE id18657476_gitpass;
USE id18657476_gitpass;

CREATE TABLE login(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE passwords(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    site VARCHAR(255) NOT NULL,
    username VARCHAR(100),
    password VARCHAR(100) NOT NULL,
    favorite BOOLEAN DEFAULT FALSE,
    user_id INT DEFAULT 1,
    foreign key (user_id) references login(id)
);
