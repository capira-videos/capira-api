-- user

CREATE TABLE IF NOT EXISTS capira_user(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    session_token VARCHAR(255) NOT NULL,
    registered_at TIMESTAMP NOT NULL DEFAULT 0,
    last_login TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS capira_user_profile(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    user_name varchar(60) NOT NULL UNIQUE,
    email varchar(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES capira_user(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS capira_user_godmode(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    FOREIGN KEY(id) 
        REFERENCES capira_user_profile(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

-- channel 

CREATE TABLE IF NOT EXISTS content(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS content_hirachic(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    parent BIGINT UNSIGNED NOT NULL,
    instructor BIGINT UNSIGNED NOT NULL,
    published ENUM('private','unlisted','public') NOT NULL,
    lang VARCHAR(8) default 'en',
    description TEXT,
    FOREIGN KEY(id) 
        REFERENCES content(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(instructor) 
        REFERENCES capira_user(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(parent) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS channel(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    title VARCHAR(255),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;


-- lesson

CREATE TABLE IF NOT EXISTS lesson(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS video(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    src VARCHAR(255),
    type ENUM('yt','html5') NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;




-- Permissions 

CREATE TABLE IF NOT EXISTS permission(
    content_hirachic_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    level TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY(content_hirachic_id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(user_id) 
        REFERENCES capira_user(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;



-- Overlay

CREATE TABLE IF NOT EXISTS overlay(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    lesson_id BIGINT UNSIGNED NOT NULL,
    type ENUM(
            'standard-annotation',
            'short-annotation',
            'short-answer-quiz',
            'single-answer-quiz',
            'multi-answer-quiz',
            'hotspot-quiz',
            'draw-quiz',
            'incontext-quiz',
            'math-quiz',
            'incontext-math-quiz') NOT NULL,
    background_color VARCHAR(32),     /* most extream value: rgba(255,255,255,0.123123) */
    background_image VARCHAR(512),    /* url */
    FOREIGN KEY(id) 
        REFERENCES content(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(lesson_id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;


CREATE TABLE IF NOT EXISTS overlay_keyframes(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    type VARCHAR(255),
    keyframe_show_at DECIMAL(10,3) NOT NULL,
    keyframe_hide_at DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    question VARCHAR(8000),
    max_attempts SMALLINT,     
    FOREIGN KEY(id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;



CREATE TABLE IF NOT EXISTS quiz_answer(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    quiz_id BIGINT UNSIGNED NOT NULL,
    index_  SMALLINT UNSIGNED,
    feedback_grade SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    feedback_text VARCHAR(8000),    
    FOREIGN KEY(id) 
        REFERENCES content(id) 
        ON DELETE CASCADE,      
    FOREIGN KEY(quiz_id) 
        REFERENCES quiz(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;


CREATE TABLE IF NOT EXISTS reaction(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content_id BIGINT UNSIGNED NOT NULL,
    type ENUM('repeat','play','seek-to','show-overlay','show-lesson') NOT NULL,
    FOREIGN KEY(content_id) 
        REFERENCES content(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS reaction_seek_to(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    keyframe DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS reaction_show_overlay(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    overlay_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(overlay_id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS reaction_show_lesson(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    lesson_id BIGINT UNSIGNED NOT NULL,
    keyframe_start DECIMAL(10,3),
    keyframe_end DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(lesson_id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* quiz_option For single-answer-quiz and multi-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_option(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    index_  SMALLINT UNSIGNED,
    caption VARCHAR(8000),          
    FOREIGN KEY(quiz_id) 
        REFERENCES quiz(id) 
        ON DELETE CASCADE,
    CONSTRAINT u_quiz_option UNIQUE (quiz_id,index_)
) ENGINE = INNODB CHARSET=utf8;

/* image-blob for hotspot-quiz and draw-quiz */
CREATE TABLE IF NOT EXISTS image_blob(
    content_id BIGINT UNSIGNED NOT NULL,
    imageData MEDIUMBLOB NOT NULL,          
    FOREIGN KEY(content_id) 
        REFERENCES content(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* single-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_answer_single_answer_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    quiz_option_id BIGINT UNSIGNED,
    FOREIGN KEY(id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_option_id) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* multi-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_answer_multi_answer_quiz(
    id BIGINT UNSIGNED NOT NULL,
    quiz_option_id BIGINT UNSIGNED,
    FOREIGN KEY(id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_option_id) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* short-answer-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_short_answer_quiz(
    answer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    case_sensitive TINYINT(1),
    typo_tolerant TINYINT(1),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* hotspot-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_hotspot_quiz(
    answer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    expected_answer VARCHAR(100),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* math-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_math_quiz(
    answer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* incontext-math-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_incontext_math_quiz(
    answer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

/* draw-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_draw_quiz(
    answer_id BIGINT UNSIGNED NOT NULL UNIQUE,
    translate_x TINYINT(1),
    translate_y TINYINT(1),
    accuracy DECIMAL(5),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS overlay_item(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    overlay_id BIGINT UNSIGNED NOT NULL UNIQUE,
    x DECIMAL(5) NOT NULL,
    y DECIMAL(5) NOT NULL,
    z TINYINT UNSIGNED NOT NULL,
    h DECIMAL(5) NOT NULL,
    w DECIMAL(5) NOT NULL,
    type VARCHAR(56) NOT NULL,
    caption TEXT,
    FOREIGN KEY(overlay_id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;



CREATE TABLE IF NOT EXISTS interaction(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id BIGINT UNSIGNED NOT NULL,
    content_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY(content_id)
        REFERENCES content(id)
        ON DELETE CASCADE,
    FOREIGN KEY(user_id)
        REFERENCES capira_user(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8; 

CREATE TABLE IF NOT EXISTS lesson_interaction(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    type enum('started','ended') NOT NULL,
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8; 
 

CREATE TABLE IF NOT EXISTS video_interaction(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    type enum('play','pause','playing','seek-to','velocity') NOT NULL,
    keyframe DECIMAL(10,3),
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8; 




CREATE TABLE IF NOT EXISTS overlay_interaction(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    type enum('show','hide','skip'),
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

-- Quiz Interactions 
CREATE TABLE IF NOT EXISTS quiz_interaction(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    quiz_answer_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY(id)
        REFERENCES overlay_interaction(id)
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_answer_id)
        REFERENCES quiz_answer(id)
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;


CREATE TABLE IF NOT EXISTS quiz_interaction_single_answer_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(given_answer) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_multi_answer_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(given_answer) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_short_answer_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer VARCHAR(2048),
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_hotspot_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer_x INT UNSIGNED NOT NULL,
    given_answer_y INT UNSIGNED NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_draw_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer MEDIUMBLOB, 
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_math_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer VARCHAR(2048),
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS quiz_interaction_incontext_math_quiz(
    id BIGINT UNSIGNED NOT NULL UNIQUE,
    given_answer TEXT, 
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;



-- views 

CREATE OR REPLACE VIEW view_channel AS 
    SELECT * FROM channel 
        NATURAL JOIN content
        NATURAL JOIN content_hirachic;

CREATE OR REPLACE VIEW view_lesson AS 
    SELECT * FROM lesson 
        NATURAL JOIN content
        NATURAL JOIN content_hirachic;

CREATE OR REPLACE VIEW view_quiz AS 
    SELECT * FROM quiz 
        NATURAL JOIN overlay
        NATURAL JOIN content;

CREATE OR REPLACE VIEW view_user AS 
    SELECT  *, 
            (SELECT COUNT(*) FROM capira_user_godmode WHERE capira_user_godmode.id = capira_user.id) as godmode
        FROM capira_user 
        NATURAL LEFT JOIN capira_user_profile;




DROP TRIGGER IF EXISTS insert_channel_trigger;
delimiter |
CREATE TRIGGER insert_channel_trigger BEFORE INSERT ON channel
  FOR EACH ROW
  BEGIN
      INSERT INTO content VALUES();
      SET new.id := (SELECT last_insert_id());
      INSERT INTO content_hirachic (id,parent,instructor) VALUES(new.id,1,1);
  END;

DROP TRIGGER IF EXISTS insert_lesson_trigger;
delimiter $$
CREATE TRIGGER insert_lesson_trigger BEFORE INSERT ON lesson
  FOR EACH ROW
  BEGIN
      INSERT INTO content VALUES();
      SET new.id := (SELECT last_insert_id());
      INSERT INTO content_hirachic (id,parent,instructor) VALUES(new.id,1,1);
  END;
$$ delimiter ;


-- test users
INSERT INTO capira_user (session_token,registered_at) VALUES('session_token-1',null);
INSERT INTO capira_user_profile (id,user_name,email,password) VALUES(last_insert_id(),'username-1','email-1@email.de','secret-password-1');

INSERT INTO capira_user (session_token,registered_at) VALUES('session_token-2',null);
INSERT INTO capira_user_profile (id,user_name,email,password) VALUES(last_insert_id(),'username-2','email-2@email.de','secret-password-2');
INSERT INTO capira_user_godmode (id) VALUES(last_insert_id());

-- test channels
INSERT INTO content VALUES();
INSERT INTO content_hirachic (id,parent,instructor) VALUES(last_insert_id(),last_insert_id(),1);
INSERT INTO channel (id,title) VALUES(last_insert_id(),'channel-title-1');

INSERT INTO content VALUES();
INSERT INTO content_hirachic (id,parent,instructor) VALUES(last_insert_id(),1,1);
INSERT INTO channel (id,title) VALUES(last_insert_id(),'channel-title-2');


-- test lesson 
INSERT INTO content VALUES();
INSERT INTO content_hirachic (id,parent,instructor) VALUES(last_insert_id(),1,1);
INSERT INTO lesson (id) VALUES(last_insert_id()); 
INSERT INTO video (id,src) VALUES(last_insert_id(),'aZCIa2sdf'); 

-- test overlays
INSERT INTO content VALUES();
INSERT INTO overlay (id,lesson_id) VALUES(last_insert_id(),(SELECT id FROM lesson LIMIT 1));
INSERT INTO quiz (id,question) VALUES(last_insert_id(),'To be or not to be?');
