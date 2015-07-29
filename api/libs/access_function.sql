-- This function is used to determine the next parent channel that gives the user any permissions

DELIMITER $$
DROP FUNCTION IF EXISTS GetNextLevel $$
CREATE FUNCTION GetNextLevel (user_id INT, channel_id INT) RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE rv INT;
    DECLARE ui INT;
    DECLARE ch INT;

    SET rv = -1;
    SET ch = channel_id;
    WHILE ch > 0 DO
        SELECT ifnull(parent,-1), ifnull(level,-1) INTO ch, rv FROM
        (SELECT parent, level FROM Channels LEFT JOIN ChannelAccess ON channelid = id AND userid = user_id WHERE id = ch) A;
        IF rv > 0 THEN
            SET ch = -1;
        END IF;
    END WHILE;
    RETURN rv;
END -- $$
-- DELIMITER ;