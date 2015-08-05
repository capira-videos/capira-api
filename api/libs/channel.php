<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

function getUnits($id, $fetchPermissions = false) {
	global $mysqli, $user;

	$query = "SELECT Units.id,Units.title,Units.videoId,Units.published,ChannelUnits.viewIndex,COALESCE(p.correct/p.layers,p.viewed) AS progress, IF(Units.homechannel=?, TRUE, FALSE) as isHomeChannel FROM ChannelUnits
			  RIGHT JOIN Units ON (ChannelUnits.unitId=Units.id)
			  LEFT JOIN UnitProgress p ON p.unitId = Units.id AND p.userId=?
			  WHERE ChannelUnits.channelId=?" . ($fetchPermissions ? "" : " AND published=1") . " ORDER BY ChannelUnits.viewIndex";
	$stmt = $mysqli->prepare($query);
	$userid = $user->userid();
	$stmt->bind_param("iii", $id, $userid, $id);
	$stmt->execute();
	$stmt->store_result();
	$units = array();
	$unit = get_result($stmt);

	while ($stmt->fetch()) {
		if ($fetchPermissions) {
			$unit['admin'] = $user->has_privilege($unit['id'], ADMIN, false);
		}
		$unit['isHomeChannel'] = (bool) $unit['isHomeChannel'];
		$units[] = $unit;
		$unit = get_result($stmt);
	}
	$stmt->free_result();
	$stmt->close();

	return $units;
}

function getPath($channel) {
	global $mysqli;

	// cached in db
	$sql = 'SELECT breadcrumb FROM BreadcrumbCache WHERE channelid = ?';
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("i", $channel);
	$stmt->execute();
	$stmt->bind_result($breadcrumb);
	if ($stmt->fetch()) {
		$stmt->close();
		return unserialize($breadcrumb);
	}

	$stmt->close();

	// newly generated
	$sql = "SELECT T2.id, T2.title, T1.lvl
			FROM (
			    SELECT
			        @r AS _id,
			        (SELECT @r := parent FROM Channels WHERE id = _id) AS parent,
			        @l := @l + 1 AS lvl
			    FROM
			        (SELECT @r := " . intval($channel) . ", @l := 0) vars,
			        Channels m
			    WHERE @r <> 0) T1
			JOIN Channels T2
			ON T1._id = T2.id
			ORDER BY T1.lvl ASC";

	$stmt = $mysqli->prepare($sql);
	$stmt->execute();
	$stmt->bind_result($id, $title, $lvl);

	$breadcrumbs = array();
	while ($stmt->fetch()) {
		$breadcrumbs[] = array('id' => $id, 'title' => $title);
	}

	$stmt->close();

	// remove own
	array_shift($breadcrumbs);

	$breadcrumb = serialize($breadcrumbs);

	// store
	$sql = 'INSERT IGNORE INTO BreadcrumbCache (channelid, breadcrumb) VALUES (?, ?)';
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("is", $channel, $breadcrumb);
	$stmt->execute();
	$stmt->close();

	return $breadcrumbs;
}

function getChannel($id, $sendPermissions = false) {

	global $user, $mysqli;

	$len = strlen($id);
	$id[$len - 1] = $id[$len - 1] != '/' ? $id[$len - 1] : '';

	$path = explode('/', $id);

	$id = intval($path[count($path) - 1]);

	//Set Jörn's Channel as root
	if ($id == 0) {
		$id = 1;
	}
	$userid = $user->userid();

	$query = "SELECT id,title,parent,description FROM Channels WHERE id=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $id);
	$stmt->execute();

	$channel = get_result($stmt);
	$stmt->fetch();
	$stmt->close();

	$query = "SELECT Channels.id,Channels.title,Channels.viewIndex,Channels.published,Channels.parent,Channels.description,ThumbnailCache.thumbnail,p.progress
			  FROM Channels
			  LEFT JOIN ThumbnailCache ON ThumbnailCache.channelId=Channels.id
			  LEFT JOIN ChannelProgress p ON p.channelId = Channels.id AND p.userId=?
			  WHERE Channels.parent=?" . ($sendPermissions ? "" : " AND published=1") . " ORDER BY Channels.viewIndex";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ii", $userid, $id);
	$stmt->execute();
	$stmt->store_result();

	$subchannels = array();

	$subchannel = get_result($stmt);

	while ($stmt->fetch()) {
		/*
		Where is this informations necessary? Just legacy from old system? Can be large overhead...
		if ($sendPermissions) && $user->has_privilege($subchannel['id'], AUTHOR)) {
		$subchannel['author'] = true;
		}
		 */
		if ($subchannel['thumbnail'] == null) {
			$subchannel['thumbnail'] = getThumbnailRecursiveCache($subchannel['id']);
		}

		$subchannels[] = $subchannel;
		$subchannel = get_result($stmt);
	}
	$stmt->free_result();
	$stmt->close();

	$channel['channels'] = $subchannels;
	$channel['units'] = getUnits($id);
	$channel['path'] = getPath($id);

	if ($sendPermissions) {
		if ($user->has_privilege($channel['id'], ADMIN)) {
			$channel['admin'] = true;
		}
		if ($user->has_privilege($channel['parent'], AUTHOR)) {
			$channel['parentAuthor'] = true;
		}
	}
	//You may want to know in the Channel if User can access the Channel-Editor.
	//That's why we send it, even if not the full permissions have been requested.
	if ($user->has_privilege($channel['id'], AUTHOR)) {
		$channel['author'] = true;
	}

	return $channel;
}

// retrieves a thumbnail and caches it
function getThumbnailRecursiveCache($channelid) {
	global $mysqli;
	$thumbnail = getThumbnailRecursive($channelid);

	// cache it
	if ($thumbnail !== null) {
		$sql = 'INSERT INTO ThumbnailCache (channelId, thumbnail) VALUES (?, ?)';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('is', $channelid, $thumbnail);
		$res = $stmt->execute();
	}

	return $thumbnail;
}

// only works for trees
function getThumbnailRecursive($channelid) {
	global $mysqli;
	// try fetching a thumbnail from the channel
	$query = "SELECT Units.videoId as thumbnail
				 FROM ChannelUnits
				 JOIN Units ON (ChannelUnits.unitId=Units.id)
				 WHERE ChannelUnits.channelId=?
				 LIMIT 1";

	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $channelid);
	$stmt->execute();

	$stmt->store_result();

	$stmt->bind_result($thumbnail);

	// try to fetch
	$result = $stmt->fetch();
	$stmt->free_result();
	$stmt->close();

	// got a result
	if ($result) {
		return $thumbnail;
	} else {
		// find subchannels and query there
		$query = "SELECT Channels.id
			 FROM Channels
			 WHERE Channels.parent=?";

		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $channelid);
		$stmt->execute();

		$stmt->store_result();

		$subid = null;
		$subt = null;
		$stmt->bind_result($subid);

		// try to fetch
		while ($stmt->fetch()) {
			// depth first search
			$subt = getThumbnailRecursive($subid);
			if ($subt !== null) {
				break;
			}
		}

		$stmt->free_result();
		$stmt->close();

		return $subt;
	}
	return null;
}

function createChannel($channel) {
	$parent = $channel['parent'];
	$title = $channel['title'];
	if (!isset($parent) || $parent == "" || !isset($title) || $title == "") {
		malformed_request('missing parent or title');
	}
	check_channel_privileges($parent, AUTHOR);

	global $mysqli, $user;
	$query = "INSERT INTO Channels(title,parent) VALUES(?,?)";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("si", $title, $parent)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$channel['id'] = $stmt->insert_id;

	$stmt->close();

	return $channel;
}

function deleteChannel($channel) {
	//TODO: delete channels recursively
	if (!isset($channel['id'])) {
		malformed_request('missing parent or id');
	}

	check_channel_privileges($channel['id'], AUTHOR);
	$channelId = $channel['id'];
	global $mysqli;
	$query = "DELETE FROM Channels WHERE id=?";

	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("i", $channelId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();

}

function updateChannel($channel) {
	if (!isset($channel['id'])) {
		malformed_request('Missing id');
	}
	check_channel_privileges($channel['id'], AUTHOR);

	global $mysqli;
	$query = "UPDATE Channels SET title=?, published=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("sii", $channel['title'], $channel['published'], $channel['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();

	return $channel;
}

function updateChannelParent($channel) {
	if (!isset($channel['parent']) || !isset($channel['id'])) {
		malformed_request('Missing parent or id');
	}
	check_channel_privileges($channel['id'], AUTHOR);

	check_channel_privileges($channel['parent'], AUTHOR);

	global $mysqli;
	$query = "UPDATE Channels SET parent=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ii", $channel['parent'], $channel['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();

	// invalidate breadcrumbs
	$query = "DELETE FROM BreadcrumbCache WHERE breadcrumb LIKE ?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	$search = '%s:2:"id";i:' . intval($channel['id']) . ';%';
	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("s", $search)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();

	return $channel;
}

function updateOrder($channel) {
	check_channel_privileges($channel['id'], AUTHOR);

	global $mysqli;

	$query = "UPDATE Channels SET viewIndex=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	$i = 0;
	$id = 0;

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ii", $i, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	foreach ($channel['channels'] as $subchannel) {
		$id = $subchannel['id'];
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}

		$i++;
	}

	$stmt->close();

	$query = "UPDATE ChannelUnits SET viewIndex=? WHERE channelId=? AND unitId=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	$i = 0;
	$id = 0;
	$channelId = $channel['id'];

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("iii", $i, $channelId, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	foreach ($channel['units'] as $unit) {
		$id = $unit['id'];
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$i++;
	}

	$stmt->close();

	return $channel;
}

?>
