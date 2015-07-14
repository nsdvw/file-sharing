CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    hash VARCHAR(100) NOT NULL,
    salt VARCHAR(100) NOT NULL,
    registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (email),
    PRIMARY KEY (id)
);

CREATE TABLE file (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    upload_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(255),
    author_id INT UNSIGNED,
    size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    download_counter INT UNSIGNED NOT NULL DEFAULT 0,
    mediaInfo TEXT COMMENT 'specific params, packed in json-format string',
    FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (id)
);