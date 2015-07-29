<?php
include_once 'common.php';
include_once 'libraries/unit.php';
echo json_encode(getUnit($_GET['id'],$_GET['folder']));
$mysqli->close();
?>

