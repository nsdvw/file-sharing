CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nickname VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    registration_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
    /* e.g. application/vnd.openxmlformats-officedocument.presentationml.presentation */
    mediaInfo TEXT COMMENT 'specific params, packed in json-format string',
    FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (id)
);