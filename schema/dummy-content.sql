
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
