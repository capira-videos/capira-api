<?php

exit;

require('common.php');

function list_groups($channel) {
    global $mysqli;

    $sql = 'SELECT g.id, p.level FROM Groups g
            JOIN ChannelPermissions p ON p.groupid = g.id
            WHERE p.channelid=?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $channel);
    $stmt->execute();

    $stmt->bind_result($id, $level);

    // try to fetch
    $list = array();
    while($stmt->fetch()) {
        $list[] = array('id' => $id, 'level' => $level);
    }
    
    $stmt->close();

    return $list;
}

function insert_channel_permissions($channel, $group, $level) {
    global $mysqli;

    $sql = 'SELECT userid FROM UserGroups WHERE groupid=?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $group);
    $stmt->execute();

    $stmt->bind_result($id);

    // try to fetch
    $list = array();
    while($stmt->fetch()) {
        $list[] = $id;
    }
    
    $stmt->close();

    $sql = 'INSERT INTO ChannelAccess (channelid, userid, level) VALUES (?, ?, ?)';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('iii', $channel, $userid, $level);
    
    foreach ($list as $userid) {
        $stmt->execute();
    }
    
    $stmt->close();
}

function recursive_call($channel, $current_permissions) {
    global $mysqli;

    // then work on channels
    // 1. find out which groups have currently what level of access here
    $data = list_groups($channel);

    // 2. check if permissions already present
    foreach ($data as $permissions) {
        if(array_key_exists($permissions['id'], $current_permissions) && $current_permissions[$permissions['id']] == $permissions['level']) {
            // already in there
        } else {
            // put in database
            insert_channel_permissions($channel, $permissions['id'], $permissions['level']);
            $current_permissions[$permissions['id']] = $permissions['level'];
        }
    }

    // recurse
    $sql = 'SELECT id FROM Channels WHERE parent = ?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $channel);
    $stmt->execute();

    $stmt->bind_result($id);

    $list = array();
    while($stmt->fetch()) {
        $list[] = $id;
    }

    $stmt->close();

    foreach ($list as $id) {
        // recurse
        recursive_call($id, $current_permissions);
    }
}

recursive_call(-1, array());

?>
