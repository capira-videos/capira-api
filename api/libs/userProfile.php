<?php
require 'userServer.php';

if (!$user->check_angular_request()) {
	exit;
}

$username = $_GET['username'];

$method = $_SERVER['REQUEST_METHOD'];

function getProgress($username) {
	global $mysqli;
	$sql = '
	  SELECT UnitProgress.unitId,title, IF(layers=0,100,100*correct/layers) as completed FROM UserData
          	JOIN UnitProgress
          		ON UnitProgress.userId=UserData.id
          	JOIN Units
          		ON Units.id=UnitProgress.unitId
          	WHERE UserData.name=?';
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->store_result();
	$unitProgress = array();
	$progress = get_result($stmt);

	while ($stmt->fetch()) {
		$unitProgress[] = $progress;
		$progress = get_result($stmt);
	}
	$stmt->free_result();
	$stmt->close();
	return $unitProgress;
}

switch ($method) {
case 'GET':
	if (isset($username) && $username == $user->name()) {
		// find out groups of this user
		$sql = '
			  SELECT Channels.id,title,admin FROM Channels
					JOIN ChannelPermissions
						ON Channels.id=ChannelPermissions.channelid
					JOIN UserGroups
						ON ChannelPermissions.groupid=UserGroups.groupid
					JOIN UserData
						ON UserData.id=UserGroups.userid
						AND UserData.name=?
					GROUP BY Channels.id';

		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		$channels = array();
		$channel = get_result($stmt);

		while ($stmt->fetch()) {
			$channels[] = $channel;
			$channel = get_result($stmt);
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode(array('username' => $username, 'channels' => $channels, 'progress' => getProgress($username)));
	} else {
		echo "{\"error\":404}";
	}
	break;
case 'POST':
	$group = json_decode(file_get_contents("php://input"), true);

	break;
}
exit;

//Check if the current user owns this profile
//yes: display private profile
//no: display public profile

//Private Profile
/* Query all Groups of this user
SELECT UserGroups.groupid, Groups.name, UserGroups.admin FROM UserGroups JOIN Groups ON UserGroups.groupid=Groups.id WHERE UserGroups.userid=8
 */

/* Query all Channels with Permissions
SELECT id,title,admin FROM Channels
JOIN ChannelPermissions
ON Channels.id=ChannelPermissions.channelid
JOIN UserGroups
ON ChannelPermissions.groupid=UserGroups.groupid
AND UserGroups.userid=8
GROUP BY id
 */

//Edit Profile
//change PW
//change email

//Public Profile
//Progress?

?>