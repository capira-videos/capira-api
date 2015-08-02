<?php

if (!defined('VALID_INCLUDE')) {
	exit;
}

function queryString($query, $start = 0, $limit = false) {
	global $mysqli, $user;

	$terms = explode(' ', $query);
	$userid = $user->userid();

	$a_param_type = array('i');
	$a_bind_params = array($userid);
	$where_units = array();
	foreach ($terms as $term) {
		$likely = '%' . str_replace('%', '\%', $term) . '%';
		$a_param_type[] = 's';
		$a_bind_params[] = $likely;
		$a_param_type[] = 's';
		$a_bind_params[] = $likely;
		$where_units[] = '(t.title LIKE ? OR u.title LIKE ?)';
	}

	$channel = array('id' => null, 'title' => null, 'parent' => null);

	$sql = 'SELECT DISTINCT u.id, u.title, u.videoId, 0 AS viewIndex, (p.correct/p.layers) AS progress
			FROM Units u
			LEFT JOIN UnitTags m ON m.unitId = u.id
			LEFT JOIN Tags t ON t.id = m.tagId
			LEFT JOIN UnitProgress p ON p.unitId = u.id AND p.userId=?
			WHERE ' . implode(' AND ', $where_units);

	if ($limit !== false) {
		$limit = intval($limit);
		$start = intval($start);
		$sql .= ' LIMIT ' . $limit . ' OFFSET ' . $start;
	}

	/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
	$a_params = array();

	$param_type = '';
	$n = count($a_param_type);
	for ($i = 0; $i < $n; $i++) {
		$param_type .= $a_param_type[$i];
	}

	/* with call_user_func_array, array params must be passed by reference */
	$a_params[] = &$param_type;

	for ($i = 0; $i < $n; $i++) {
		/* with call_user_func_array, array params must be passed by reference */
		$a_params[] = &$a_bind_params[$i];
	}

	$stmt = $mysqli->prepare($sql);
	/* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
	call_user_func_array(array($stmt, 'bind_param'), $a_params);
	$stmt->execute();

	$stmt->store_result();

	$count_units = $stmt->num_rows;

	$units = array();
	$unit = get_result($stmt);

	while ($stmt->fetch()) {
		$units[] = $unit;
		$unit = get_result($stmt);
	}
	$stmt->free_result();
	$stmt->close();

	$channel['units'] = $units;

	$a_param_type = array('i');
	$a_bind_params = array($userid);
	$where_channels = array();
	foreach ($terms as $term) {
		$likely = '%' . str_replace('%', '\%', $term) . '%';
		$a_param_type[] = 's';
		$a_bind_params[] = $likely;
		$a_param_type[] = 's';
		$a_bind_params[] = $likely;
		$a_param_type[] = 's';
		$a_bind_params[] = $likely;
		$where_channels[] = '(c.title LIKE ? OR c.description LIKE ? OR t.title LIKE ?)';
	}

	$sql = 'SELECT DISTINCT c.id, c.title, c.description, 0 AS viewIndex, tc.thumbnail, p.progress
			FROM Channels c
			LEFT JOIN ChannelTags m ON m.channelId = c.id
			LEFT JOIN Tags t ON t.id = m.tagId
			LEFT JOIN ThumbnailCache tc ON tc.channelId=c.id
			LEFT JOIN ChannelProgress p ON p.channelId = c.id AND p.userId=?
			WHERE ' . implode(' AND ', $where_channels);

	if ($limit !== false) {
		$sql .= ' LIMIT ' . ($limit - $count_units); // no offset?
	}

	$stmt = $mysqli->prepare($sql);

	/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
	$a_params = array();

	$param_type = '';
	$n = count($a_param_type);
	for ($i = 0; $i < $n; $i++) {
		$param_type .= $a_param_type[$i];
	}

	/* with call_user_func_array, array params must be passed by reference */
	$a_params[] = &$param_type;

	for ($i = 0; $i < $n; $i++) {
		/* with call_user_func_array, array params must be passed by reference */
		$a_params[] = &$a_bind_params[$i];
	}

	$stmt = $mysqli->prepare($sql);
	/* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
	call_user_func_array(array($stmt, 'bind_param'), $a_params);

	$stmt->execute();

	$stmt->store_result();

	$subchannels = array();

	$subchannel = get_result($stmt);

	while ($stmt->fetch()) {
		$subchannels[] = $subchannel;
		$subchannel = get_result($stmt);
	}
	$stmt->free_result();
	$stmt->close();

	$channel['channels'] = $subchannels;

	return $channel;
}

?>
