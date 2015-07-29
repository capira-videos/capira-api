<?php
include_once '../common.php';
include_once '../libraries/unit.php';
if(!isset($_GET['pretty'])){
    echo json_encode(getUnit($_GET['id']));
}else{
	   /*XSS VULNERBILITY! */
    echo '<textarea style="width:100%; height:100%;">'.json_encode(getUnit($_GET['id']),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).'</textarea>';
}
	$mysqli->close();
?>