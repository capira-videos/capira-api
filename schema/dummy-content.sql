
-- test users

INSERT INTO user (session_token,registered_at) VALUES('session_token-1',null);
INSERT INTO user_profile (id,user_name,email,password) VALUES(last_insert_id(),'username-1','email-1@email.de','secret-password-1');

INSERT INTO user (session_token,registered_at) VALUES('session_token-2',null);
INSERT INTO user_profile (id,user_name,email,password) VALUES(last_insert_id(),'username-2','email-2@email.de','secret-password-2');
INSERT INTO user_godmode (id) VALUES(last_insert_id());

-- test channels
INSERT INTO content (created_at) VALUES(null);
INSERT INTO content_hirachic (id,parent,instructor) VALUES(last_insert_id(),last_insert_id(),1);
INSERT INTO channel (id,title) VALUES(last_insert_id(),'channel-title-1');

INSERT INTO content (created_at) VALUES(null);
INSERT INTO content_hirachic (id,parent,instructor) VALUES(last_insert_id(),1,1);
INSERT INTO channel (id,title) VALUES(last_insert_id(),'channel-title-2');
