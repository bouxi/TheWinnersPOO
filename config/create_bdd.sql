CREATE TABLE users
(
    id        INT AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(50),
    email     VARCHAR(100),
    password  VARCHAR(255),
    role      VARCHAR(20),
    birthdate DATE NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    avatar    VARCHAR(255) NULL
);

CREATE TABLE tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    id_author INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_author) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tip_id INT NOT NULL,
    author VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tip_id) REFERENCES tips(id) ON DELETE CASCADE
);


