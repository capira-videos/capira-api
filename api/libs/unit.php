<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

function getUnit($id, $channel = false) {
	global $mysqli;
	if ($id == 0) {
		$id = 2;
	}
	$query = "SELECT  Units.*, GROUP_CONCAT(Tags.title) as tags,downloadId
			FROM Units
			LEFT JOIN (UnitTags,Tags) ON (UnitTags.tagId=Tags.id AND UnitTags.unitId=?)
			LEFT JOIN DownloadLinks ON DownloadLinks.unitId=?
			WHERE Units.id=?";

	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("iii", $id, $id, $id);

	$stmt->execute();

	$data = get_result($stmt);
	$stmt->fetch();

	$unit = unserialize($data['unitblob']);
	if (!isset($unit['id'])) {
		$unit['id'] = $id;
	}

	$stmt->close();

	if ($channel !== false) {
		// fetch next unit
		$sql = 'SELECT c.unitId
				FROM ChannelUnits c
				JOIN ChannelUnits t ON t.unitId=? AND t.channelId=c.channelId
				WHERE c.channelId=? AND c.viewIndex>=t.viewIndex AND c.unitId <> t.unitId
				ORDER BY c.viewIndex
				LIMIT 1';

		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('ii', $id, $channel);
		$stmt->execute();

		$stmt->bind_result($next);

		// try to fetch
		$unit['next'] = null;
		if ($stmt->fetch()) {
			$unit['next'] = $next;
		}

		$stmt->close();
	}
	global $user;

	$unit['admin'] = $user->has_privilege($unit['id'], AUTHOR, false);

	return $unit;

}

function addUnitToChannel($unitId, $channelId) {
	global $mysqli;

	$query = "INSERT INTO ChannelUnits(channelId,unitId) VALUES(?,?)";

	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ii", $channelId, $unitId);

	$stmt->execute();
	$stmt->close();
}

function updateUnit($unit) {

	if (!isset($unit['overlays'])) {
		return updateUnitTitle($unit);
	}
	global $mysqli;

	check_unit_privileges($unit['id'], AUTHOR);
	unset($unit['admin']);

	$query = "UPDATE Units SET title=?, videoId=?, unitblob=? WHERE id=?";

	$stmt = $mysqli->prepare($query);

	/*
	// delete and create
	if (isset($unit['overlays'])) {
	$overlays = array();
	$maxLayerId = 0;
	$maxItemId = 0;
	// determine max id's
	foreach ($unit['overlays'] as $layer) {
	if (isset($layer['id'])) {
	$maxLayerId = max($maxLayerId, $layer['id']);
	foreach ($layer['items'] as $item) {
	if (isset($item['id'])) {
	$maxItemId = max($maxItemId, $item['id']);
	}
	}
	}
	}

	// then delete and set ids
	foreach ($unit['overlays'] as $layer) {
	if (isset($layer['deleted']) && ($layer['deleted'])) {
	continue;
	}
	if (!isset($layer['id'])) {
	$layer['id'] = ++$maxLayerId;
	}
	foreach ($layer['items'] as $item) {
	if (isset($item['deleted']) && ($item['deleted'])) {
	continue;
	}
	if (!isset($item['id'])) {
	$item['id'] = ++$maxItemId;
	}
	}
	}
	}
	 */

	if (!isset($unit['authorId'])) {
		$unit['authorId'] = 0;
	}
	if (!isset($unit['videoId'])) {
		$unit['videoId'] = "";
		if ($unit['video']) {
			$unit['videoId'] = $unit['video']['source'];
		}
	}
	if (!isset($unit['title'])) {
		$unit['title'] = "";
	}
	$blob = serialize($unit);
	$stmt->bind_param("sssi", $unit['title'], $unit['videoId'], $blob, $unit['id']);

	$stmt->execute();
	$stmt->close();
	return $unit;
}

function updateUnitTitle($unit) {
	global $mysqli;

	check_unit_privileges($unit['id'], AUTHOR);
	unset($unit['admin']);

	$query = "UPDATE Units SET title=? WHERE id=?";

	$stmt = $mysqli->prepare($query);

	$stmt->bind_param("si", $unit['title'], $unit['id']);

	$stmt->execute();
	$stmt->close();
	return $unit;
}

function deleteUnit($unitId) {
	check_unit_privileges($unitId, AUTHOR);

	global $mysqli;
	$query = "DELETE FROM Units WHERE id=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $unitId);
	$stmt->execute();
	$stmt->close();
}

function deleteUnitFromChannel($unitId, $channelId) {
	check_channel_privileges($channelId, AUTHOR);

	global $mysqli;
	$query = "DELETE FROM ChannelUnits WHERE channelId=? AND unitId=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ii", $channelId, $unitId);
	$stmt->execute();
	$stmt->close();

}

function createUnit($unit) {
	global $mysqli, $user;

	unset($unit['admin']);
	if (isset($unit['parent'])) {
		$parent = $unit['parent'];
		check_channel_privileges($parent, AUTHOR);
		$query = "INSERT INTO Units(title,videoId,authorId,homechannel,unitblob) VALUES(?,?,?,?,?) ";
	} else {
		$query = "INSERT INTO Units(title,videoId,authorId,unitblob) VALUES(?,?,?,?) ";
	}

	$stmt = $mysqli->prepare($query);

	if (!isset($unit['authorId'])) {
		$unit['authorId'] = 0;
	}
	if (!isset($unit['videoId'])) {
		$unit['videoId'] = "";
		if ($unit['video']) {
			$unit['videoId'] = $unit['video']['source'];
		}
	}
	if (!isset($unit['title'])) {
		$unit['title'] = "";
	}
	$blob = serialize($unit);
	if (isset($unit['parent'])) {
		$stmt->bind_param("sssis", $unit['title'], $unit['videoId'], $unit['authorId'], $parent, $blob);
	} else {
		$test = $stmt->bind_param("ssss", $unit['title'], $unit['videoId'], $unit['authorId'], $blob);
	}

	$stmt->execute();
	$unit['id'] = $stmt->insert_id;
	$stmt->close();

	if ($unit['id'] != 0) {
		if (isset($unit['parent'])) {
			require 'permissionManagement.php';
			$manager = new Permissions($user);

			addUnitToChannel($unit['id'], $unit['parent']);
		}

	}
	return $unit;
}

function updateUnitParent($unit) {
	if (!isset($unit['parent']) || !isset($unit['id']) || !isset($unit['oldParent'])) {
		malformed_request('Missing parent, oldParent or id');
	}

	check_channel_privileges($unit['parent'], AUTHOR);
	check_channel_privileges($unit['oldParent'], AUTHOR);

	deleteUnitFromChannel($unit['id'], $unit['oldParent']);
	addUnitToChannel($unit['id'], $unit['parent']);
}

?>
