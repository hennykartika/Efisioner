-- Efisioner schema. Tested on MariaDB 10.11; compatible with MySQL 8.0.16+.
-- Import into an empty database:  mysql -u root -p efisioner < database/schema.sql

SET NAMES utf8mb4;

CREATE TABLE users (
    id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username         VARCHAR(32)     NOT NULL,
    email            VARCHAR(254)    NOT NULL,
    password_hash    VARCHAR(255)    NOT NULL,
    role             ENUM ('user', 'admin') NOT NULL DEFAULT 'user',
    full_name        VARCHAR(100)    NOT NULL,
    phone            VARCHAR(20)     NULL,
    address          VARCHAR(500)    NULL,
    birth_date       DATE            NOT NULL,
    gender           ENUM ('male', 'female') NOT NULL,
    education        ENUM ('primary', 'junior_high', 'senior_high', 'diploma_3', 'diploma_4', 'bachelor', 'master', 'doctorate') NOT NULL,
    occupation       ENUM ('civil_servant', 'military', 'police', 'private_sector', 'self_employed', 'student', 'other') NOT NULL,
    occupation_other VARCHAR(100)    NULL,
    institution      VARCHAR(150)    NULL,
    reward_balance   BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Price is not stored here: it is derived from target x reward + platform fee,
-- and the amount actually charged lives in payments.gross_amount.
CREATE TABLE surveys (
    id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    owner_id              INT UNSIGNED NOT NULL,
    title                 VARCHAR(150) NOT NULL,
    description           TEXT         NOT NULL,
    target_respondents    INT UNSIGNED NOT NULL,
    reward_per_respondent INT UNSIGNED NOT NULL,
    remaining_slots       INT UNSIGNED NOT NULL,
    paid_at               DATETIME     NULL,
    closed_at             DATETIME     NULL,
    created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_surveys_owner (owner_id),
    KEY idx_surveys_open (paid_at, closed_at, remaining_slots),
    CONSTRAINT fk_surveys_owner FOREIGN KEY (owner_id) REFERENCES users (id),
    CONSTRAINT chk_surveys_slots CHECK (remaining_slots <= target_respondents)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE questions (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    survey_id  INT UNSIGNED NOT NULL,
    body       VARCHAR(500) NOT NULL,
    option_a   VARCHAR(200) NOT NULL,
    option_b   VARCHAR(200) NOT NULL,
    option_c   VARCHAR(200) NULL,
    option_d   VARCHAR(200) NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_questions_survey (survey_id),
    CONSTRAINT fk_questions_survey FOREIGN KEY (survey_id) REFERENCES surveys (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Respondent details are copied at submission time so that later profile
-- edits do not change research data that was already collected.
CREATE TABLE responses (
    id               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    survey_id        INT UNSIGNED     NOT NULL,
    respondent_id    INT UNSIGNED     NOT NULL,
    respondent_name  VARCHAR(100)     NOT NULL,
    gender           ENUM ('male', 'female') NOT NULL,
    age              TINYINT UNSIGNED NOT NULL,
    education        ENUM ('primary', 'junior_high', 'senior_high', 'diploma_3', 'diploma_4', 'bachelor', 'master', 'doctorate') NOT NULL,
    occupation       ENUM ('civil_servant', 'military', 'police', 'private_sector', 'self_employed', 'student', 'other') NOT NULL,
    occupation_other VARCHAR(100)     NULL,
    institution      VARCHAR(150)     NULL,
    submitted_at     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_responses_survey_respondent (survey_id, respondent_id),
    KEY idx_responses_respondent (respondent_id),
    CONSTRAINT fk_responses_survey FOREIGN KEY (survey_id) REFERENCES surveys (id) ON DELETE CASCADE,
    CONSTRAINT fk_responses_respondent FOREIGN KEY (respondent_id) REFERENCES users (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE answers (
    response_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    choice      ENUM ('A', 'B', 'C', 'D') NOT NULL,
    PRIMARY KEY (response_id, question_id),
    KEY idx_answers_question (question_id, choice),
    CONSTRAINT fk_answers_response FOREIGN KEY (response_id) REFERENCES responses (id) ON DELETE CASCADE,
    CONSTRAINT fk_answers_question FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Ledger of every change to users.reward_balance. Credits are positive,
-- payouts are negative. The balance column is a cached sum of this table.
CREATE TABLE reward_transactions (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED    NOT NULL,
    survey_id   INT UNSIGNED    NULL,
    description VARCHAR(255)    NOT NULL,
    amount      BIGINT          NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reward_transactions_user (user_id, created_at),
    CONSTRAINT fk_reward_transactions_user FOREIGN KEY (user_id) REFERENCES users (id),
    CONSTRAINT fk_reward_transactions_survey FOREIGN KEY (survey_id) REFERENCES surveys (id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- One row per checkout attempt. target/reward are snapshotted so the survey is
-- activated with exactly what was paid for. No cascade: financial records stay.
CREATE TABLE payments (
    id                     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    survey_id              INT UNSIGNED    NOT NULL,
    order_id               VARCHAR(50)     NOT NULL,
    target_respondents     INT UNSIGNED    NOT NULL,
    reward_per_respondent  INT UNSIGNED    NOT NULL,
    gross_amount           BIGINT UNSIGNED NOT NULL,
    status                 ENUM ('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
    gateway_status         VARCHAR(32)     NULL,
    payment_type           VARCHAR(32)     NULL,
    gateway_transaction_id VARCHAR(64)     NULL,
    redirect_url           VARCHAR(500)    NULL,
    paid_at                DATETIME        NULL,
    created_at             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_payments_order (order_id),
    KEY idx_payments_survey (survey_id, status),
    CONSTRAINT fk_payments_survey FOREIGN KEY (survey_id) REFERENCES surveys (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip_address    VARCHAR(45)     NOT NULL,
    username_hash CHAR(64)        NOT NULL,
    attempted_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_login_attempts_ip (ip_address, attempted_at),
    KEY idx_login_attempts_time (attempted_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
