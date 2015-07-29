<?php

if(!defined('VALID_INCLUDE')) {
	exit;
}

function setProgressViewed($unit) {
	check_logged_in();
	global $mysqli, $user;
	// then for unit
	$sql = 'INSERT INTO UnitProgress (userId,unitId,correct,layers,viewed)
				SELECT ?, ?, IF(COUNT(*)=0,1,COUNT(successes)), IF(COUNT(*)=0,1,COUNT(*)), 1
				FROM Layers
				LEFT JOIN Progress ON id = layerId AND successes > 0 AND userid=`
				WHERE parent = ?
			ON DUPLICATE KEY UPDATE 
			correct=VALUES(correct), 
			layers=VALUES(layers),
			viewed=1';
			
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($sql))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$userid = $user->userid();
	$stmt->bind_param('iii', $userid, $unit, $unit);
	$res = $stmt->execute();
	
	$stmt->close();
	
	updateChannels($unit, true);
}

function setProgress($layer, $time, $success,$score) {
	check_logged_in();
	global $mysqli, $user;
	
	$userid = $user->userid();
	$successes = $success ? 1 : 0;
	$failures = 1-$successes;

	// first for layer	
	$sql = 'INSERT INTO Progress (userId,layerId,successes,failures,averageTime,bestTime,bestScore)
			VALUES (?,?,?,?,?,?,?)
			ON DUPLICATE KEY UPDATE ' .
			(($success) ? 'successes=successes+1' : 'failures=failures+1') . ', 
			averageTime=(averageTime*(successes+failures)+VALUES(averageTime))/(successes+failures+1),
			bestTime=LEAST(bestTime,VALUES(bestTime)),
			bestScore=GREATEST(bestScore,VALUES(bestScore))';
			
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($sql))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->bind_param('iiiiiid', $userid, $layer, $successes, $failures, $time, $time, $score);
	$res = $stmt->execute();
	
	$stmt->close();
	
	// find out unit
	$sql = 'SELECT parent FROM Layers WHERE id=?';
	
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i', $layer);
	$stmt->execute();
	$stmt->bind_result($parent);
	$stmt->fetch();
	$stmt->close();
	
	// then for unit
	$sql = 'INSERT INTO UnitProgress (userId,unitId,correct,layers)
				SELECT ?, parent, COUNT(successes), COUNT(*)
				FROM Layers
				LEFT JOIN Progress ON id = layerId AND successes > 0 AND userid =?
				WHERE parent = ?
			ON DUPLICATE KEY UPDATE 
			correct=VALUES(correct), 
			layers=VALUES(layers)';
			
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($sql))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$stmt->bind_param('iii', $userid, $userid, $parent);
	$res = $stmt->execute();
	
	$stmt->close();

	updateChannels($parent, true);
}

function updateChannels($id, $unit=false) {
	check_logged_in();
	global $user,$mysqli;
	$channelquery = $unit === true ? 'SELECT channelId FROM ChannelUnits WHERE unitId = ?' : 'SELECT parent FROM Channels WHERE id = ?';

	// then for channels
	$sql = 'INSERT INTO ChannelProgress (userId,channelId,progress)
				SELECT ?, c.id,
				((
					SELECT COALESCE(SUM(p.progress),0) FROM ChannelProgress p
					JOIN Channels c1 ON c1.id = p.channelId
					WHERE c1.parent = c.id AND p.userId=?
				)
				+
				(
					SELECT COALESCE(SUM(p.correct) / SUM(p.layers),0) FROM UnitProgress p
					JOIN ChannelUnits u ON u.unitId = p.unitId
					WHERE u.channelId = c.id AND p.userId=?
				))
				/
				((
					SELECT COUNT(*) FROM Channels c1
					WHERE c1.parent = c.id
				)
				+
				(
					SELECT COUNT(*) FROM ChannelUnits u
					WHERE u.channelId = c.id
				))
				FROM Channels c
				WHERE c.id IN (' . $channelquery . ')
			ON DUPLICATE KEY UPDATE 
			progress=VALUES(progress)';
			
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli->prepare($sql))) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$userid = $user->userid();
	$stmt->bind_param('iiii', $userid, $userid, $userid, $id);
	$res = $stmt->execute();
	
	$stmt->close();
	
	// upper channels
	$stmt = $mysqli->prepare($channelquery);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$stmt->store_result();

	$stmt->bind_result($parent);

	// try to fetch
	while($stmt->fetch()) {
		updateChannels($parent);
	}
	
	$stmt->free_result();
	$stmt->close();

}

function getProgressChannel($channel, $verbose=true) {
	check_logged_in();
	global $mysqli, $user;

	$sql = 'SELECT progress FROM ChannelProgress WHERE channelId=? AND userId=?';
	
	$stmt = $mysqli->prepare($sql);
	$userid = $user->userid();
	$stmt->bind_param('ii', $channel, $userid);
	$stmt->execute();

	$stmt->bind_result($progress);

	// try to fetch
	$result = array('progress' => 0);
	if($stmt->fetch()) {
		$result['progress'] = $progress;
	}
	
	$stmt->close();
	
	if($verbose)
		echo json_encode($result);
	return $result;
}

function getProgressUnit($unit, $details = false, $verbose=true) {
	check_logged_in();
	global $mysqli, $user;

	$sql = 'SELECT correct, layers FROM UnitProgress WHERE unitId=? AND userId=?';
	
	$stmt = $mysqli->prepare($sql);
	$userid = $user->userid();
	$stmt->bind_param('ii', $unit, $userid);
	$stmt->execute();

	$stmt->bind_result($correct, $layers);

	// try to fetch
	$result = array('correct' => 0, 'layers' => 1);
	if($stmt->fetch()) {
		$result['correct'] = $correct;
		$result['layers'] = $layers;
	}
	
	$stmt->close();
	
	// exit
	if(!$details) {
		if($verbose)
			echo json_encode($result);
		return $result;
	}
	
	$sql = 'SELECT layerId, successes, failures, averageTime, bestTime, (SELECT AVG(bestScore) FROM Progress p WHERE p.layerId = id) AS averageScore
			FROM Progress
			JOIN Layers ON layerId = id
			WHERE parent=? AND userId=?';
	
	$stmt = $mysqli->prepare($sql);
	$userid = $user->userid();
	$stmt->bind_param('ii', $unit, $userid);
	$stmt->execute();

	$stmt->bind_result($layer, $successes, $failures, $avg, $best, $score);

	// try to fetch
	$result['layer_results'] = array();
	while($stmt->fetch()) {
		$result['layer_results'][] = array(
			'layerId' => $layer,
			'successes' => $successes,
			'failures' => $failures,
			'averageTime' => $avg,
			'bestTime' => $best,
			'score' => $score
		);
	}
	
	$stmt->close();
	
	if($verbose)
		echo json_encode($result);
	return $result;
}

?>