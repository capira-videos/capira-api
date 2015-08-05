<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

class User {

	private $_userid;
	private $_logged_in;
	private $_username;

	/**
	 * This constructor initialises the user and tries to load an opened session.
	 */
	public function __construct() {
		// start a session
		// session_start(); moved to common.php
		$this->_userid = -1;
		$this->_logged_in = false;
		// and check if the user is logged in (validate everything)
		$this->validate_login();
	}

	/**
	 * This function tries to load an opened session.
	 */
	private function validate_login() {
		global $mysqli;
		// check if the user is logged in
		if (isset($_SESSION['session_token'])) {
			// if the session is invalidated, logout user
			if (!isset($_COOKIE['XSRF-TOKEN']) || $_COOKIE['XSRF-TOKEN'] != $_SESSION['session_token']) {
				$this->logout();
				return;
			}

			// potentially logged in, so check against the database
			$sql = 'SELECT Users.id, ud.name
					FROM Users
					LEFT JOIN UserData ud ON Users.id=ud.id
					WHERE session_token=?';
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('s', $_SESSION['session_token']);
			$stmt->execute();

			$id = 0;
			$name = null;
			$stmt->bind_result($id, $name);

			// try to fetch
			$stmt->fetch();
			$stmt->close();

			// validate user id
			if ($_SESSION['id'] == $id) {
				$this->_userid = $_SESSION['id'];
				$this->_logged_in = ($name != null && !empty($name));
				$this->_username = $name;
				if (!$this->_logged_in) {
					$sql = 'UPDATE `Users` SET `last_login`=CURRENT_TIMESTAMP WHERE id=?';
					$stmt = $mysqli->prepare($sql);
					$stmt->bind_param('i', $id);
					$stmt->execute();
					$stmt->close();
				}
				return;
			}
		}

		$this->_logged_in = false;
		$this->_userid = ANONYMOUS;

		$this->registerAnonymous();
	}

	/**
	 * This function checks against the XSRF-header of angular js.
	 */
	public function check_angular_request() {
		return (isset($_SESSION['session_token']) && isset($_SERVER['HTTP_X_XSRF_TOKEN']) && isset($_COOKIE['XSRF-TOKEN']) && $_SESSION['session_token'] == $_SERVER['HTTP_X_XSRF_TOKEN'] && $_COOKIE['XSRF-TOKEN'] == $_SERVER['HTTP_X_XSRF_TOKEN']);
	}

	/**
	 * This function sets the session token cookie for angular js.
	 */
	private function set_session_token($anonymous = false) {
		// set a cookie that contains the token
		if ($anonymous) {
			setcookie('XSRF-TOKEN', $_SESSION['session_token'], time() + 3600 * 24 * 30, '/');
		}
		//expire in 30 days
		else {
			setcookie('XSRF-TOKEN', $_SESSION['session_token'], time() + 3600 * 24 * 3, '/');
		}
		//expire in 3 days
	}

	// Returns if the user is logged in.
	public function logged_in() {
		return $this->_logged_in;
	}

	// Returns the userid.
	public function userid() {
		return $this->_userid;
	}

	//Returns the username
	public function name() {
		return $this->_username;
	}

	// Returns if User is Anonymous.
	public function isAnonymous() {
		return !(isset($this->_username) && $this->_username != null && $this->_username != "");
	}

	public function json_object() {
		global $mysqli;

		$query = "SELECT id, name, email, godmode
				FROM UserData
				WHERE id=?";
		/* Prepared statement, stage 1: prepare */
		if (!($stmt = $mysqli->prepare($query))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}

		/* Prepared statement, stage 2: bind and execute */
		if (!$stmt->bind_param("i", $this->_userid)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		$user = get_result($stmt);
		$stmt->fetch();

		$stmt->close();

		$user['id'] = $this->_userid;

		return json_encode($user);
	}

	/**
	 * This function logs a user in.
	 */
	public function login($name, $password) {

		if (!isset($name) || $name == "" || !isset($password) || $password == "") {
			malformed_request('missing <code>name</code> or <code>password</code>');
		}
		global $mysqli;
		$sql = 'SELECT password, id FROM UserData WHERE name=?';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $name);
		$stmt->execute();

		$hashed = '';
		$id = 0;
		$stmt->bind_result($hashed, $id);

		// try to fetch
		if (!$stmt->fetch()) {
			$stmt->close();
			return FAILED;
		}

		$stmt->close();

		// then verify and return result including session_token
		$result = password_verify($password, $hashed);

		if ($result) {
			$_SESSION['session_token'] = uniqid('', true);
			$_SESSION['id'] = $id;
			$this->_logged_in = true;
			$this->_userid = $id;

			// login user if ok
			$sql = 'UPDATE Users SET session_token=? WHERE Users.id=(SELECT UserData.id FROM UserData WHERE name=?)';
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('ss', $_SESSION['session_token'], $name);
			$stmt->execute();
			$stmt->close();

			$this->set_session_token();
			return SUCCESS;
		} else {
			$this->logout();
		}
		return FAILED;
	}

	/**
	 * This function logs a user out.
	 */
	public function logout() {
		global $mysqli;
		$logged_in = $this->_logged_in;
		$this->_logged_in = false;

		// Falls die Session gelöscht werden soll, löschen Sie auch das
		// Session-Cookie.
		// Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}

		// really destroy xsrf token
		setcookie('XSRF-TOKEN', '', time() - 42000, '/');

		// Zum Schluß, löschen der Session.
		session_destroy();

		// only if really logged in
		if (!$logged_in) {
			return LOGGED_OUT;
		}

		$sql = 'UPDATE Users SET session_token=? WHERE id=?';
		$stmt = $mysqli->prepare($sql);
		$empty = '';
		$stmt->bind_param('ss', $empty, $this->_userid);
		$stmt->execute();
		$stmt->close();
		$this->_userid = ANONYMOUS;
		// set anonymous

		// Tell UI we've succeeded
		return LOGGED_OUT;
	}

	/**
	 * This function registers a new user.
	 */
	public function register($name, $email, $password) {
		global $mysqli;

		check_valid_email($email);

		$loginpw = $password;
		$password = password_hash($password, PASSWORD_DEFAULT);
		$empty = '';

		// check for name
		$query = "SELECT id FROM UserData WHERE name=?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$stmt->bind_result($id);
		if ($stmt->fetch()) {
			header("HTTP/1.1 406 Not Acceptable");
			echo NOT_NAME_UNIQUE;
			exit;
		}

		$stmt->close();

		if ($this->_userid < 0 || $this->_logged_in) {
			$sql = 'INSERT INTO Users (session_token) VALUES (?)';
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('s', $empty);
			$res = $stmt->execute();
			$insertId = $mysqli->insert_id;
		} else {
			$insertId = $this->_userid;
			$res = true;
		}

		if ($res) {
			$sql = 'INSERT INTO UserData (id,name, email, password) VALUES (?,?, ?, ?)';
			$stmt = $mysqli->prepare($sql);
			$stmt->bind_param('isss', $insertId, $name, $email, $password);
			$res = $stmt->execute();
		}

		if ($res) {
			$this->login($name, $loginpw);
			unset($loginpw);
			return $this;
		}

		header("HTTP/1.1 406 Not Acceptable");
		echo NOT_EMAIL_UNIQUE;
		exit;
	}

	/**
	 * This function registers a new anonymous user.
	 */
	public function registerAnonymous() {
		global $mysqli;

		$_SESSION['session_token'] = uniqid('', true);
		$this->_logged_in = false;

		// login user if ok
		$sql = 'INSERT INTO Users (session_token) VALUES (?)';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $_SESSION['session_token']);
		$stmt->execute();
		$stmt->close();
		$id = $mysqli->insert_id;
		$this->set_session_token(true);

		$_SESSION['id'] = $id;
		$this->_userid = $id;

	}

	public function has_privilege($id, $privilege, $is_channel = true) {
		//TODO: Server crashes when channelId doesn't exist!!
		global $mysqli;
		if (!$this->_logged_in) {
			return $privilege == ANONYMOUS;
		}

		if ($privilege == ANONYMOUS) {
			return true;
		}

		$userid = $this->_userid;

		// see if user is godmode or owner
		$sql = 'SELECT COALESCE(us.godmode,0), c.owner FROM UserData us, ' . ($is_channel ? 'Channels' : 'Units') . ' c WHERE c.id = ? AND us.id = ?';
		$stmt = $mysqli->prepare($sql);

		$stmt->bind_param('ii', $id, $userid);
		$stmt->execute();
		$stmt->store_result();

		$stmt->bind_result($godmode, $owner);

		// try to fetch
		$stmt->fetch();
		$stmt->free_result();
		$stmt->close();

		// done!
		if ($godmode == 1 || $owner == $this->_userid) {
			return true;
		}

		// if is unit, first try at unit level before switching to channel permissions
		if (!$is_channel) {
			$sql = 'SELECT level FROM UnitAccess WHERE unitid = ? AND userid = ?';
			$stmt = $mysqli->prepare($sql);

			$stmt->bind_param('ii', $id, $userid);
			$stmt->execute();
			$stmt->store_result();

			$level = ANONYMOUS;
			$stmt->bind_result($level);

			// try to fetch
			$found = $stmt->fetch();
			$stmt->free_result();
			$stmt->close();

			// admin or privilege
			if ($found) {
				return $level >= $privilege;
			}

			// ok, now switch over to channels
			// first find out homechannel
			$sql = 'SELECT homechannel FROM Units WHERE id = ?';
			$stmt = $mysqli->prepare($sql);

			$unitid = $id;
			$stmt->bind_param('i', $unitid);
			$stmt->execute();
			$stmt->store_result();

			$stmt->bind_result($id);

			// try to fetch
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
		}

		// $sql = 'SELECT MAX(p.level), COALESCE(us.godmode,0), c.owner FROM ' . ($is_channel ? 'ChannelPermissions' : 'UnitPermissions') . ' p
		// 		JOIN UserGroups u ON p.groupid = u.groupid
		// 		JOIN UserData us ON us.id = u.userid
		// 		JOIN ' . ($is_channel ? 'Channels' : 'Units') . ' c ON c.id = p.' . ($is_channel ? 'channel' : 'unit') . 'id

		// 		WHERE p.' . ($is_channel ? 'channel' : 'unit') . 'id=? AND u.userid=?';
		$sql = 'SELECT GetNextLevel(?, ?) AS level';
		$stmt = $mysqli->prepare($sql);

		$stmt->bind_param('ii', $userid, $id);

		$stmt->execute();
		$stmt->store_result();

		$level = ANONYMOUS;
		$stmt->bind_result($level);

		// try to fetch
		$stmt->fetch();
		$stmt->free_result();
		$stmt->close();

		// admin or privilege
		return $level >= $privilege;
	}

}
?>
