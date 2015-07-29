<?php
require 'common.php';

global $mysqli;
global $user;
$userId = $user -> userid();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
	case 'GET' :
		$sql = 'SELECT Comments.id, (DATE_FORMAT(commentTime,  "%d.%m.%Y %H:%i:%s")) as time,name as user,comment, IFNULL(SUM(isPositive),0) as positive, IFNULL(COUNT(isPositive)-SUM(isPositive),0) as negative, CommentAnswers.answerTo as answerTo,
		IFNULL((SELECT (isPositive-1)+isPositive FROM CommentRating WHERE id=Comments.id AND userId=?),0) as voted
                					FROM Comments
                					    LEFT JOIN UserData
                        					ON Comments.userId=UserData.Id
                        				LEFT JOIN CommentRating
                        					ON Comments.id=CommentRating.id
                        				LEFT JOIN CommentAnswers
                                			ON CommentAnswers.id=Comments.id
                       					WHERE unitId=?
                        				GROUP BY Comments.id';
		$stmt = $mysqli -> prepare($sql);
		$stmt -> bind_param('ii',$userId,$_GET['unit']);
		$stmt -> execute();
		$result = array();
		$comment = get_result($stmt);
		while ($stmt -> fetch()) {
			$result[] = $comment;
			$comment = get_result($stmt);
		}

		$stmt -> fetch();
		$stmt -> close();
		echo json_encode($result);
		break;
	case 'POST' :
	    if(!$user->isAnonymous()){
		$request = json_decode(file_get_contents("php://input"), true);
		if (isset($request['comment'])) {
			insert_comment($userId, $request['comment'], $request['unit']);
			return;
		}
		if (isset($request['update'])) {
        	update_comment($userId, $request['update'],$request['id']);
        	return 'success';
        }
		if (isset($request['response'])) {
			$id=insert_comment($userId, $request['response'], $request['unit']);

			$query = "INSERT INTO CommentAnswers(id,answerTo) VALUES(?,?)";
			/* Prepared statement, stage 1: prepare */
			if (!($stmt = $mysqli -> prepare($query))) {
				echo "Prepare failed: (" . $mysqli -> errno . ") " . $mysqli -> error;
			}

			/* Prepared statement, stage 2: bind and execute */
			if (!$stmt -> bind_param("ii", $id,$request['responseTo'])) {
				echo "Binding parameters failed: (" . $stmt -> errno . ") " . $stmt -> error;
			}

			if (!$stmt -> execute()) {
				echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
			}
			return;
		}
		if (isset($request['commentUp'])) {
			rate_comment($request['commentUp'], $userId, 1);
			return 'success';
		}
		if (isset($request['commentDown'])) {
			rate_comment($request['commentDown'], $userId, 0);
			return 'success';
		}
		if (isset($request['commentNeutral'])) {
        			delete_rating($request['commentNeutral'], $userId);
        			return 'success';
        		}
        }
		break;

}

function insert_comment($userId, $comment, $unitId) {
	global $mysqli;
	
	$query = "INSERT INTO Comments(userId,comment,unitId) VALUES(?,?,?)";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli -> prepare($query))) {
		echo "Prepare failed: (" . $mysqli -> errno . ") " . $mysqli -> error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt -> bind_param("isi", $userId, $comment, $unitId)) {
		echo "Binding parameters failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}

	if (!$stmt -> execute()) {
		echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}
	$id=$stmt->insert_id;
	echo  json_encode(["id"=>$id]);
	return $id;
}

function update_comment($userId, $newComment,$commentId){
    global $mysqli;

	$query = "UPDATE Comments SET comment=? WHERE userId=? AND id=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli -> prepare($query))) {
		echo "Prepare failed: (" . $mysqli -> errno . ") " . $mysqli -> error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt -> bind_param("sii", $newComment, $userId, $commentId)) {
		echo "Binding parameters failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}

	if (!$stmt -> execute()) {
		echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}
}

function rate_comment($commentId,$userId,$positive) {
	global $mysqli;
	
	$query = "INSERT INTO CommentRating(id,userId,isPositive) VALUES(?,?,?) ON DUPLICATE KEY UPDATE isPositive=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli -> prepare($query))) {
		echo "Prepare failed: (" . $mysqli -> errno . ") " . $mysqli -> error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt -> bind_param("iiii", $commentId,$userId, $positive,$positive)) {
		echo "Binding parameters failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}

	if (!$stmt -> execute()) {
		echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}
	
	return $stmt->insert_id;
}

function delete_rating($commentId,$userId){
	global $mysqli;

	$query = "DELETE FROM CommentRating WHERE id=? AND userId=?";
	/* Prepared statement, stage 1: prepare */
	if (!($stmt = $mysqli -> prepare($query))) {
		echo "Prepare failed: (" . $mysqli -> errno . ") " . $mysqli -> error;
	}

	/* Prepared statement, stage 2: bind and execute */
	if (!$stmt -> bind_param("ii", $commentId,$userId)) {
		echo "Binding parameters failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}

	if (!$stmt -> execute()) {
		echo "Execute failed: (" . $stmt -> errno . ") " . $stmt -> error;
	}

	return $stmt->insert_id;
}
?>


