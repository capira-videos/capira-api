<?php
	include_once '../common.php';
	include_once '../libraries/unit.php';

    $unit = json_decode(file_get_contents("php://input"),true);

   if(isset($unit['id'])){
   		updateUnit($unit);	   
   } else {
   		$unit = createUnit($unit);
   }

	
	echo json_encode(getUnit($unit['id']));
		$mysqli->close();
	
?>