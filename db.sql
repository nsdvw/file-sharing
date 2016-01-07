
CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    hash VARCHAR(100) NOT NULL,
    salt VARCHAR(100) NOT NULL,
    registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (email),
    UNIQUE (login),
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_bin;

CREATE TABLE file (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    upload_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    author_id INT UNSIGNED,
    size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    download_counter INT UNSIGNED NOT NULL DEFAULT 0,
    mediaInfo TEXT COMMENT 'specific params, packed in json string',
    author_token VARCHAR(100) COMMENT 'to identify unregistered users',
    best_before TIMESTAMP NOT NULL COMMENT 'expiration time',
    FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX ix_token (author_token),
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_bin;

CREATE TABLE comment (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    contents TEXT NOT NULL,
    file_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED,
    materialized_path VARCHAR(255) NOT NULL,
    parent_id INT UNSIGNED,
    added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES file (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES user (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comment (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8 COLLATE = utf8_bin;
