CREATE OR REPLACE FUNCTION set_update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
   NEW.last_update = now(); 
   RETURN NEW;
END;
$$ language 'plpgsql';

-- user

CREATE TABLE IF NOT EXISTS capira_user(
    id SERIAL PRIMARY KEY,
    session_token VARCHAR(255) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS capira_user_profile(
    id INTEGER NOT NULL UNIQUE,
    user_name varchar(60) NOT NULL UNIQUE,
    email varchar(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES capira_user(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS capira_user_godmode(
    id INTEGER NOT NULL UNIQUE,
    FOREIGN KEY(id) 
        REFERENCES capira_user_profile(id) 
        ON DELETE CASCADE
);


CREATE TRIGGER set_last_update_user BEFORE UPDATE OR INSERT
ON capira_user FOR EACH ROW EXECUTE PROCEDURE
set_update_timestamp();

-- channel 

CREATE TABLE IF NOT EXISTS content(
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TYPE published AS ENUM ('private','unlisted','public');
CREATE TABLE IF NOT EXISTS content_hirachic(
    id INTEGER NOT NULL UNIQUE,
    parent INTEGER NOT NULL,
    instructor INTEGER NOT NULL,
    published published NOT NULL DEFAULT 'private',
    lang VARCHAR(8) default 'en',
    thumbnail_uri VARCHAR(128) NOT NULL DEFAULT '',
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
);

CREATE TABLE IF NOT EXISTS channel(
    id INTEGER NOT NULL UNIQUE,
    title VARCHAR(255),
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE
);


CREATE TRIGGER set_last_update_channel BEFORE UPDATE OR INSERT
    ON channel FOR EACH ROW EXECUTE PROCEDURE
    set_update_timestamp();

-- lesson

CREATE TABLE IF NOT EXISTS lesson(
    id INTEGER NOT NULL UNIQUE,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE
);

CREATE TYPE video_type AS ENUM ('yt','html5');
CREATE TABLE IF NOT EXISTS video(
    id INTEGER NOT NULL UNIQUE,
    src VARCHAR(255),
    type video_type NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
);




CREATE TRIGGER set_last_update_lesson BEFORE UPDATE OR INSERT
    ON lesson FOR EACH ROW EXECUTE PROCEDURE
    set_update_timestamp();


-- Permissions 

CREATE TABLE IF NOT EXISTS permission(
    content_hirachic_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    level NUMERIC NOT NULL,
    FOREIGN KEY(content_hirachic_id) 
        REFERENCES content_hirachic(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(user_id) 
        REFERENCES capira_user(id) 
        ON DELETE CASCADE
);



-- Overlay
CREATE TYPE overlay_type AS ENUM (
            'standard-annotation',
            'short-annotation',
            'short-answer-quiz',
            'single-answer-quiz',
            'multi-answer-quiz',
            'hotspot-quiz',
            'draw-quiz',
            'incontext-quiz',
            'math-quiz',
            'incontext-math-quiz');

CREATE TABLE IF NOT EXISTS overlay(
    id INTEGER NOT NULL UNIQUE,
    lesson_id INTEGER NOT NULL,
    type overlay_type NOT NULL,
    background_color VARCHAR(32),     /* most extream value: rgba(255,255,255,0.123123) */
    background_image VARCHAR(512),    /* url */
    FOREIGN KEY(id) 
        REFERENCES content(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(lesson_id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS overlay_keyframes(
    id INTEGER NOT NULL UNIQUE,
    type VARCHAR(255),
    keyframe_show_at DECIMAL(10,3) NOT NULL,
    keyframe_hide_at DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz(
    id INTEGER NOT NULL UNIQUE,
    question VARCHAR(8000),
    max_attempts SMALLINT,     
    FOREIGN KEY(id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS quiz_answer(
    id INTEGER NOT NULL UNIQUE,
    quiz_id INTEGER NOT NULL,
    index_  SMALLINT,
    feedback_grade SMALLINT NOT NULL DEFAULT 0,
    feedback_text VARCHAR(8000),    
    FOREIGN KEY(id) 
        REFERENCES content(id) 
        ON DELETE CASCADE,      
    FOREIGN KEY(quiz_id) 
        REFERENCES quiz(id) 
        ON DELETE CASCADE
);

CREATE TYPE reaction_type AS ENUM ('repeat','play','seek-to','show-overlay','show-lesson');
CREATE TABLE IF NOT EXISTS reaction(
    id SERIAL PRIMARY KEY,
    content_id INTEGER NOT NULL,
    type reaction_type NOT NULL,
    FOREIGN KEY(content_id) 
        REFERENCES content(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reaction_seek_to(
    id INTEGER NOT NULL UNIQUE,
    keyframe DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reaction_show_overlay(
    id INTEGER NOT NULL UNIQUE,
    overlay_id INTEGER NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(overlay_id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reaction_show_lesson(
    id INTEGER NOT NULL UNIQUE,
    lesson_id INTEGER NOT NULL,
    keyframe_start DECIMAL(10,3),
    keyframe_end DECIMAL(10,3),
    FOREIGN KEY(id) 
        REFERENCES reaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(lesson_id) 
        REFERENCES lesson(id) 
        ON DELETE CASCADE
);

/* quiz_option For single-answer-quiz and multi-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_option(
    id SERIAL PRIMARY KEY,
    quiz_id INTEGER NOT NULL,
    index_  SMALLINT,
    caption VARCHAR(8000),          
    FOREIGN KEY(quiz_id) 
        REFERENCES quiz(id) 
        ON DELETE CASCADE,
    CONSTRAINT u_quiz_option UNIQUE (quiz_id,index_)
);

/* image-blob for hotspot-quiz and draw-quiz */
CREATE TABLE IF NOT EXISTS image_blob(
    content_id INTEGER NOT NULL,
    imageData BYTEA NOT NULL,          
    FOREIGN KEY(content_id) 
        REFERENCES content(id)
        ON DELETE CASCADE
);

/* single-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_answer_single_answer_quiz(
    id INTEGER NOT NULL UNIQUE,
    quiz_option_id BIGINT,
    FOREIGN KEY(id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_option_id) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
);

/* multi-answer-quiz */

CREATE TABLE IF NOT EXISTS quiz_answer_multi_answer_quiz(
    id INTEGER NOT NULL,
    quiz_option_id BIGINT,
    FOREIGN KEY(id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_option_id) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
);

/* short-answer-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_short_answer_quiz(
    answer_id INTEGER NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    case_sensitive NUMERIC(1),
    typo_tolerant NUMERIC(1),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
);

/* hotspot-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_hotspot_quiz(
    answer_id INTEGER NOT NULL UNIQUE,
    expected_answer VARCHAR(100),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
);

/* math-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_math_quiz(
    answer_id INTEGER NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
);

/* incontext-math-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_incontext_math_quiz(
    answer_id INTEGER NOT NULL UNIQUE,
    expected_answer VARCHAR(2048),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
);

/* draw-quiz */
CREATE TABLE IF NOT EXISTS quiz_answer_draw_quiz(
    answer_id INTEGER NOT NULL UNIQUE,
    translate_x NUMERIC(1),
    translate_y NUMERIC(1),
    accuracy DECIMAL(5),
    FOREIGN KEY(answer_id) 
        REFERENCES quiz_answer(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS overlay_item(
    id SERIAL PRIMARY KEY,
    overlay_id INTEGER NOT NULL UNIQUE,
    x DECIMAL(5) NOT NULL,
    y DECIMAL(5) NOT NULL,
    z NUMERIC(3) NOT NULL,
    h DECIMAL(5) NOT NULL,
    w DECIMAL(5) NOT NULL,
    type VARCHAR(56) NOT NULL,
    caption TEXT,
    FOREIGN KEY(overlay_id) 
        REFERENCES overlay(id) 
        ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS interaction(
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER NOT NULL,
    content_id INTEGER NOT NULL,
    FOREIGN KEY(content_id)
        REFERENCES content(id)
        ON DELETE CASCADE,
    FOREIGN KEY(user_id)
        REFERENCES capira_user(id)
        ON DELETE CASCADE
); 

CREATE TYPE lesson_interaction_type AS ENUM ('started','ended');
CREATE TABLE IF NOT EXISTS lesson_interaction(
    id INTEGER NOT NULL UNIQUE,
    type lesson_interaction_type NOT NULL,
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
); 
 


CREATE TYPE video_interaction_type AS ENUM ('play','pause','playing','seek-to','velocity');
CREATE TABLE IF NOT EXISTS video_interaction(
    id INTEGER NOT NULL UNIQUE,
    type video_interaction_type NOT NULL,
    keyframe DECIMAL(10,3),
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
); 



CREATE TYPE overlay_interaction_type AS ENUM ('show','hide','skip');

CREATE TABLE IF NOT EXISTS overlay_interaction(
    id INTEGER NOT NULL UNIQUE,
    type overlay_interaction_type,
    FOREIGN KEY(id)
        REFERENCES interaction(id)
        ON DELETE CASCADE
);

-- Quiz Interactions 
CREATE TABLE IF NOT EXISTS quiz_interaction(
    id INTEGER NOT NULL UNIQUE,
    quiz_answer_id INTEGER NOT NULL,
    FOREIGN KEY(id)
        REFERENCES overlay_interaction(id)
        ON DELETE CASCADE,
    FOREIGN KEY(quiz_answer_id)
        REFERENCES quiz_answer(id)
        ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS quiz_interaction_single_answer_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer INTEGER NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(given_answer) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_multi_answer_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer INTEGER NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE,
    FOREIGN KEY(given_answer) 
        REFERENCES quiz_option(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_short_answer_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer VARCHAR(2048),
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_hotspot_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer_x INT NOT NULL,
    given_answer_y INT NOT NULL,
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_draw_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer BYTEA, 
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_math_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer VARCHAR(2048),
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_interaction_incontext_math_quiz(
    id INTEGER NOT NULL UNIQUE,
    given_answer TEXT, 
    FOREIGN KEY(id) 
        REFERENCES quiz_interaction(id) 
        ON DELETE CASCADE
);



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


/*

DROP TRIGGER IF EXISTS insert_channel_trigger;
delimiter |
CREATE TRIGGER insert_channel_trigger BEFORE INSERT ON channel
  FOR EACH ROW
  BEGIN
      INSERT INTO content VALUES(null);
      SET new.id := (SELECT LASTVAL());
      INSERT INTO content_hirachic (id,parent,instructor) VALUES(new.id,1,1);
  END;

DROP TRIGGER IF EXISTS insert_lesson_trigger;
delimiter $$
CREATE TRIGGER insert_lesson_trigger BEFORE INSERT ON lesson
  FOR EACH ROW
  BEGIN
      INSERT INTO content VALUES(null);
      SET new.id := (SELECT LASTVAL());
      INSERT INTO content_hirachic (id,parent,instructor) VALUES(new.id,1,1);
  END;
$$ delimiter ;
*/


-- test users
INSERT INTO capira_user (session_token) VALUES('session_token-1');
INSERT INTO capira_user_profile (id,user_name,email,password) VALUES(LASTVAL(),'username-1','email-1@email.de','secret-password-1');

INSERT INTO capira_user (session_token) VALUES('session_token-2');
INSERT INTO capira_user_profile (id,user_name,email,password) VALUES(LASTVAL(),'username-2','email-2@email.de','secret-password-2');
INSERT INTO capira_user_godmode (id) VALUES(LASTVAL());

-- test channels
INSERT INTO content (created_at) VALUES(default);
INSERT INTO content_hirachic (id,parent,instructor) VALUES(LASTVAL(),LASTVAL(),1);
INSERT INTO channel (id,title) VALUES(LASTVAL(),'channel-title-1');

INSERT INTO content (created_at) VALUES(default);
INSERT INTO content_hirachic (id,parent,instructor) VALUES(LASTVAL(),1,1);
INSERT INTO channel (id,title) VALUES(LASTVAL(),'channel-title-2');


-- test lesson 
INSERT INTO content (created_at) VALUES(default);
INSERT INTO content_hirachic (id,parent,instructor) VALUES(LASTVAL(),1,1);
INSERT INTO lesson (id) VALUES(LASTVAL()); 
INSERT INTO video (id,src) VALUES(LASTVAL(),'aZCIa2sdf'); 

-- test overlays
INSERT INTO content (created_at) VALUES(default);
INSERT INTO overlay (id,lesson_id) VALUES(LASTVAL(),(SELECT id FROM lesson LIMIT 1));
INSERT INTO quiz (id,question) VALUES(LASTVAL(),'To be or not to be?');
