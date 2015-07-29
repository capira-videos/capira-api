<?php

exit;

require('common.php');

function list_groups($unit) {
    global $mysqli;

    $sql = 'SELECT g.id, g.name, p.level FROM Groups g
            JOIN UnitPermissions p ON p.groupid = g.id
            WHERE p.unitid=?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $unit);
    $stmt->execute();

    $stmt->bind_result($id, $name, $level);

    // try to fetch
    $list = array();
    while($stmt->fetch()) {
        $list[] = array('id' => $id, 'name' => $name, 'level' => $level);
    }
    
    $stmt->close();

    return $list;
}

function insert_unit_permissions($unit, $group, $level, $current_permissions) {
    global $mysqli;

    echo ">> Get users\n";
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

    echo ">> Insert permissions\n";
    $sql = 'INSERT INTO UnitAccess (unitid, userid, level) VALUES (?, ?, ?)';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('iii', $unit, $userid, $level);
    
    foreach ($list as $userid) {
        if(array_key_exists($userid, $current_permissions) && $current_permissions[$userid] == $level) {
            // already in there
        } else {
            $stmt->execute();
        }
    }
    
    $stmt->close();
}

function set_unit($unit) {
    global $mysqli;

    echo "> Get homechannel's access rights\n";

    // get current access rights
    $sql = 'SELECT userid, level FROM ChannelAccess c
            JOIN Units u ON u.homechannel = c.channelid
            WHERE u.id=?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $unit);
    $stmt->execute();

    $stmt->bind_result($id, $level);

    // try to fetch
    $current_permissions = array();
    while($stmt->fetch()) {
        $current_permissions[$id] = $level;
    }
    
    $stmt->close();

    echo "> List Groups\n";
    // then work on unit
    // 1. find out which groups have currently what level of access here
    $data = list_groups($unit);

    echo "> Insert Group Permissions\n";
    // 2. check if permissions already present
    foreach ($data as $permissions) {
        insert_unit_permissions($unit, $permissions['id'], $permissions['level'], $current_permissions);
    }
}

echo "Load list of units\n";

$sql = 'SELECT id FROM Units';
$stmt = $mysqli->prepare($sql);
$stmt->execute();

$stmt->bind_result($id);

// try to fetch
$list = array();
while($stmt->fetch()) {
    $list[] = $id;
}

$stmt->close();

echo "For each unit set permissions\n";

foreach ($list as $id) {
    set_unit($id);
    echo "\n";
}

?>
