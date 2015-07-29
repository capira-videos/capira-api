<?php
function queryForUsers($query){
    global $mysqli;

    if(isset($query) && strlen($query)>2){
        $query.='%';
        $sql = "SELECT id, name FROM UserData WHERE (name LIKE ?) OR (email LIKE ?) LIMIT 5";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ss', $query,$query);
        $stmt->execute();

        $stmt->bind_result($id, $name);

        // try to fetch
        $list = array();
        while($stmt->fetch()) {
            $list[] = array('id' => $id, 'name' => $name);
        }
    $stmt->close();

    return $list;
    }

  }