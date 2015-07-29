<?php

if(!defined('VALID_INCLUDE')) {
    exit;
}

class Permissions {

    private $_user;

    public function __construct($user) {
        $this->_user = $user;
    }

    /**
     * @Deprecated
     * This function creates an empty group and returns the group id.
     */
    public function create_group($name) {
        return false;
    }

    /**
     * @Deprecated
     * This function returns an array of all groups owned by this user:
     * Array (
     *   userid => groupname
     * )
     */
    public function list_groups() {
        return array();
    }

    /**
     * @Deprecated
     * This function returns an array of all groupmembers.
     * Array (
     *   userid
     * )
     */
    public function list_group_members($userid) {
        return array();
    }

    /**
     * @Deprecated
     * This function checks if an user is group admin of a given group.
     */
    public function is_group_admin($userid) {
        return false;
    }

    /**
     * @Deprecated
     * This function adds an user to a group.
     */
    public function add_user_to_group($name, $userid) {
        return false;
    }

    /**
     * @Deprecated
     * This function sets admin status for a group user.
     */
    public function set_admin_status($userid, $userid, $is_admin) {
        return false;
    }

    /**
     * @Deprecated
     * This function removes an user from a group.
     */
    public function remove_user_from_group($userid, $userid) {
        return false;
    }

    /**
     * This function assigns permissions to a channel.
     */
    public function set_channel_permissions($channel, $userid, $isAdmin) {
        global $mysqli;

        // has to be channel admin
        if(!$this->_user->has_privilege($channel, ADMIN)) return false;

        $sql = 'INSERT INTO ChannelAccess (channelid,userid,level) VALUES (?,?,?)
                ON DUPLICATE KEY UPDATE level=VALUES(level)';

        $stmt = $mysqli->prepare($sql);
        $level=$isAdmin?100:10;
        $stmt->bind_param('iii', $channel, $userid, $level);
        $res = $stmt->execute();

        return $res;
    }

    public function list_channel_permissions($channel) {
        global $mysqli;

        if(!$this->_user->has_privilege($channel, ADMIN)) return array();

        $sql = 'SELECT a.userid, us.name, a.level FROM ChannelAccess a
                JOIN UserData us ON us.id = a.userid
                WHERE a.channelid = ?';
        $stmt = $mysqli->prepare($sql);
        $userid = $this->_user->userid();
        $stmt->bind_param('i', $channel);
        $stmt->execute();

        $stmt->bind_result($id, $name, $level);

        // try to fetch
        $list = array();
        while($stmt->fetch()) {
            $list[] = array('id' => $id, 'name' => $name, 'admin' => $level==100?true:false);
        }

        $stmt->close();

        return $list;
    }

    /**
     * This function removes permissions from a channel.
     */
    public function remove_channel_permissions($channel, $userid) {
        global $mysqli;

        // has to be channel admin
        if(!$this->_user->has_privilege($channel, ADMIN)) return false;

        $sql = 'DELETE FROM ChannelAccess WHERE channelid=? AND userid=?';
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ii', $channel, $userid);
        $res = $stmt->execute();

        return $res;
    }

    /**
     * This function assigns permissions to a unit.
     * It may not be called without security check
     */
    public function set_unit_permissions($unit, $userid, $isAdmin) {
        global $mysqli;

        // has to be unit admin
        if(!$this->_user->has_privilege($unit, ADMIN, true)) return false;

        $sql = 'INSERT INTO UnitAccess (unitid,userid,level) VALUES (?,?,?)
                ON DUPLICATE KEY UPDATE level=VALUES(level)';

        $stmt = $mysqli->prepare($sql);
        $level=$isAdmin?100:10;
        $stmt->bind_param('iii', $unit, $userid, $level);
        $res = $stmt->execute();

        return $res;
    }

    /**
     * This function removes permissions from a unit.
     */
    public function remove_unit_permissions($unit, $userid) {
        global $mysqli;

        // has to be unit admin
        if(!$this->_user->has_privilege($unit, ADMIN, true)) return false;

        $sql = 'DELETE FROM UnitAccess WHERE unitid=? AND userid=?';
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ii', $unit, $userid);
        $res = $stmt->execute();

        return $res;
    }

    public function list_unit_permissions($unit) {
        global $mysqli;

        if(!$this->_user->has_privilege($unit, ADMIN)) return array();

        $sql = 'SELECT a.userid, us.name, a.level FROM UnitAccess a
                JOIN UserData us ON us.id = a.userid
                WHERE a.unitid = ?';
        $stmt = $mysqli->prepare($sql);
        $userid = $this->_user->userid();
        $stmt->bind_param('i', $unit);
        $stmt->execute();

        $stmt->bind_result($id, $name, $level);

        // try to fetch
        $list = array();
        while($stmt->fetch()) {
            $list[] = array('id' => $id, 'name' => $name, 'admin' => $level==100?true:false);
        }

        $stmt->close();
        return $list;
    }

}

?>
