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

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (recipient_id) REFERENCES users(id)
);


ALTER TABLE users
    ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL,
    ADD COLUMN reset_token_expires_at DATETIME DEFAULT NULL;

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class VARCHAR(50) NOT NULL,
    specialization VARCHAR(50) DEFAULT NULL,
    playtime VARCHAR(100) DEFAULT NULL,
    availability TEXT DEFAULT NULL,
    motivation TEXT NOT NULL,
    submitted_at DATETIME NOT NULL,
    status ENUM('pending', 'accepted', 'refused') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

ALTER TABLE applications
    ADD COLUMN has_joined_guild BOOLEAN NOT NULL DEFAULT FALSE;

# Pas encore mise en ligne.
CREATE TABLE class_guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    spec_name VARCHAR(50) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
