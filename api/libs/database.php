<?php

if(!defined('VALID_INCLUDE')) {
	exit;
}

function db_bind_array($stmt, &$row)
{
    $md = $stmt->result_metadata();
    $params = array();
    while($field = $md->fetch_field()) {
    	if(!array_key_exists($field->name, $row))
    	{
    		$row[$field->name] = false;
    	}
        $params[] = &$row[$field->name];
    }
    return call_user_func_array(array($stmt, 'bind_result'), $params);
}

function get_result($stmt)
{
	$result = array();
    if (db_bind_array($stmt, $result) !== FALSE) {
      return $result;
    }
    return $result;
}

?>