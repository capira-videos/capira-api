<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

function insertUnitInPlaylist($unitId, $channelId) {
	global $mysqli;

	$query = "INSERT INTO ChannelUnits(channelId,unitId) VALUES(?,?)";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ii", $channelId, $unitId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$stmt->close();
}

function insertUnitIfNotExists($title, $videoId, $tags, $authorId) {
	global $mysqli;

	$query = "SELECT * FROM Units WHERE videoId=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('s', $videoId);
	$stmt->execute();

	// exists
	if ($stmt->fetch()) {
		$stmt->close();
		return false;
	}
	$stmt->close();

	$query = "INSERT INTO Units(title,videoId,authorId) VALUES(?,?,?) ";

	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("sss", $title, $videoId, $authorId)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$id = $stmt->insert_id;
	$stmt->close();

	return $id;
}

function insertAuthor($userId, $userName) {
	global $mysqli;

	$query = "SELECT id FROM Authors WHERE ytId=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('s', $userId);
	$stmt->execute();

	$id = -1;
	$stmt->bind_result($id);

	// exists
	if ($stmt->fetch()) {
		$stmt->close();
		return $id;
	}
	$stmt->close();

	$query = "INSERT INTO Authors(ytId,name) VALUES(?,?) ";

	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("ss", $userId, $userName)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$id = $stmt->insert_id;
	$stmt->close();

	return $id;
}

function insertPlaylist($ytPlaylistId, $title, $description, $authorId, $tags, $parent) {
	global $mysqli;

	$query = "SELECT id FROM Channels WHERE ytId=?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param('s', $ytPlaylistId);
	$stmt->execute();

	$id = -1;
	$stmt->bind_result($id);

	// exists
	if ($stmt->fetch()) {
		$stmt->close();
		return $id;
	}
	$stmt->close();

	$query = "INSERT INTO Channels(ytId,authorId,title,parent,description) VALUES(?,?,?,?,?)";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($query))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt->bind_param("sisis", $ytPlaylistId, $authorId, $title, $parent, $description)) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	$id = $stmt->insert_id;

	$stmt->close();

	echo $id;
}

//function insertTags(

?>