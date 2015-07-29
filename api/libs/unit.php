<?php

if (!defined('VALID_INCLUDE')) {
	exit ;
}

function getUnit($id, $folder = false) {
	global $mysqli;
	if ($id == 0) {
		$id = 2;
	}
	$query = "SELECT  Units.*, GROUP_CONCAT(Tags.title) as tags,downloadId
			FROM Units
			LEFT JOIN (UnitTags,Tags) ON (UnitTags.tagId=Tags.id AND UnitTags.unitId=?)
			LEFT JOIN DownloadLinks ON DownloadLinks.unitId=?
			WHERE Units.id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("iii", $id, $id, $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$unit = get_result($stmt);
	$stmt->fetch();

	$stmt->close();
	$unit['layers'] = getLayers($id);

	if ($folder !== false) {
		// fetch next unit
		$sql = 'SELECT c.unitId
				FROM ChannelUnits c
				JOIN ChannelUnits t ON t.unitId=? AND t.channelId=c.channelId
				WHERE c.channelId=? AND c.viewIndex>=t.viewIndex AND c.unitId <> t.unitId
				ORDER BY c.viewIndex
				LIMIT 1';

		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param('ii', $id, $folder);
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

function getLayers($id) {
	global $mysqli, $user;
	$query = "SELECT l.*, p.successes, p.failures, p.averageTime, p.bestTime, (SELECT AVG(p1.bestScore) FROM Progress p1 WHERE p1.layerId = l.id) AS averageScore
			  FROM Layers l
			  LEFT JOIN Progress p ON l.id = p.layerId AND p.userId=?
			  WHERE l.parent=?
			  ORDER BY l.start";
	$stmt = $mysqli->prepare($query);
	$userid = $user->userid();
	$stmt->bind_param("ii", $userid, $id);
	$stmt->execute();

	$layer = get_result($stmt);
	$layersRaw = array();
	while ($stmt->fetch()) {
		$layersRaw[] = $layer;
		$layer = get_result($stmt);
	}
	$stmt->close();

	$layers = array();
	foreach ($layersRaw as $layer) {
		$layers[] = getQuiz($layer);
	}

	return $layers;
}

function getQuiz($layer) {
	$layer['layerCSS'] = trim($layer['layerCSS']);
	$layer['layerJS'] = 'function test(){var x=2}';
	$layer['items'] = getItems($layer['id']);

	//$layer['progress']=0;//random_0_1();
	//$layer['tries']=0;
	//$layer['average']=random_0_1();

	return $layer;
}

function getItems($id) {
	global $mysqli;
	$query = "SELECT * FROM Items WHERE parent=?";

	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("i", $id)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	$items = array();
	$item = get_result($stmt);
	while ($stmt->fetch()) {
		$item['caption'] = trim($item['caption']);
		$items[] = $item;
		$item = get_result($stmt);
	}

	return $items;
}

function addUnitToFolder($unitId, $folderId) {
	global $mysqli;

	$query = "INSERT INTO ChannelUnits(channelId,unitId) VALUES(?,?)";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ii", $folderId, $unitId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function createLayer($layer, $parent) {
	global $mysqli;
	$query = "INSERT INTO Layers(parent,duration,start,type,interaction,classes,layerCSS) VALUES(?,?,?,?,?,?,?) ";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("iddssss", $parent, $layer['duration'], $layer['start'], $layer['type'], $layer['interaction'], $layer['classes'], $layer['layerCSS'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$layer['id'] = $stmt->insert_id;
	$stmt->close();
	foreach ($layer['items'] as $item) {
		if (!isset($item['deleted']) || !($item['deleted'])) {
			createItem($item, $layer['id']);
		}
	}
	return $layer;
}

function createItem($item, $parent) {
	global $mysqli;
	$query = " INSERT INTO Items(parent,x,y,height,width,caption,expectedValue,feedback,type,classes) VALUES(?,?,?,?,?,?,?,?,?,?) ";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("iddddsssss", $parent, $item['x'], $item['y'], $item['height'], $item['width'], $item['caption'], $item['expectedValue'], $item['feedback'], $item['type'], $item['classes'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function updateUnit($unit) {
	global $mysqli;

	check_unit_privileges($unit['id'], AUTHOR);

	$query = "UPDATE Units SET title=?, videoId=?, published=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ssii", $unit['title'], $unit['videoId'], $unit['published'], $unit['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
	if(isset($unit['layers'])){
		foreach ($unit['layers'] as $layer) {
			if (isset($layer['deleted']) && ($layer['deleted'])) {
				if (isset($layer['id']))
					deleteLayer($layer['id']);
				continue;
			}
			if (isset($layer['id'])) {
				updateLayer($layer);
			} else {
				createLayer($layer, $unit['id']);
			}
		}
		}
	return $unit;

}

function updateLayer($layer) {
	global $mysqli;
	$query = "UPDATE Layers SET duration=?, start=?, type=?, interaction=?, classes=?, layerCSS=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ddssssi", $layer['duration'], $layer['start'], $layer['type'], $layer['interaction'], $layer['classes'], $layer['layerCSS'], $layer['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();

	foreach ($layer['items'] as $item) {
		if (isset($item['deleted']) && ($item['deleted'])) {
			deleteItem($item);
			continue;
		}
		if (isset($item['id'])) {
			updateItem($item);
		} else {
			createItem($item, $layer['id']);
		}
	}
}

function updateItem($item) {
	global $mysqli;
	$query = "UPDATE Items SET x=?, y=?, height=?, width=?, caption=?, expectedValue=?, feedback=?, type=?, classes=? WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ddddsssssi", $item['x'], $item['y'], $item['height'], $item['width'], $item['caption'], $item['expectedValue'], $item['feedback'], $item['type'], $item['classes'], $item['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function deleteUnit($unit) {

}

function deleteUnitFromFolder($unitId, $folderId) {
	global $mysqli;

	$query = "DELETE FROM ChannelUnits WHERE channelId=? AND unitId=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ii", $folderId, $unitId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function deleteLayer($layerId) {
	global $mysqli;
	$query = "DELETE FROM Layers WHERE id=?";

	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("i", $layerId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
	$query = "DELETE FROM Items WHERE parent=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("i", $layerId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function deleteItem($item) {
	global $mysqli;
	$query = " DELETE FROM Items WHERE id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("i", $item['id'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function createUnit($unit) {
	global $mysqli, $user;

	if (isset($unit['parent'])) {
		$parent = $unit['parent'];
		check_channel_privileges($parent, AUTHOR);
		$query = "INSERT INTO Units(title,videoId,authorId,homechannel) VALUES(?,?,?,?) ";
	} else {
		$query = "INSERT INTO Units(title,videoId,authorId) VALUES(?,?,?) ";
	}

	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!isset($unit['authorId'])) {
		$unit['authorId'] = 0;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (isset($unit['parent'])) {
		if (!$stmt->bind_param("sssi", $unit['title'], $unit['videoId'], $unit['authorId'], $parent)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
	} else {
		if (!$stmt->bind_param("sss", $unit['title'], $unit['videoId'], $unit['authorId'])) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$unit['id'] = $stmt->insert_id;
	$stmt->close();

	if ($unit['id'] != 0) {
		if (isset($unit['parent'])) {
			require 'permissionManagement.php';
			$manager = new Permissions($user);

			addUnitToFolder($unit['id'], $unit['parent']);
		}

	}

	if (isset($unit['layers']) && count($unit['layers']) > 0) {
		return updateUnit($unit);
	}
	return $unit;
}


?>