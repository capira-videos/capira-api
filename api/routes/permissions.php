<?php
include_once 'libs/permissionManagement.php';


$manager = new Permissions($user);

$app->get('/permissions/channel/:id', function () use ($manager){
	echo json_encode(array('list' => $manager->list_channel_permissions($id)));
});

$app->post('/permissions/channel', function () use ($manager){
	$permissions = json_decode(file_get_contents("php://input"), true);
	echo json_encode($manager->set_channel_permissions($permissions['channelId'], $permissions['userId'], $permissions['isAdmin']));
});

$app->delete('/permissions/channel', function () use ($manager){
	$permissions = json_decode(file_get_contents("php://input"), true);
	echo json_encode($manager->remove_channel_permissions($permissions['channelId'], $permissions['userId']));
});


$app->get('/permissions/unit/:id', function () use ($manager){
	echo json_encode(array('list' => $manager->list_unit_permissions($id)));
});

$app->post('/permissions/unit', function () use ($manager){
	$permissions = json_decode(file_get_contents("php://input"), true);
	echo json_encode($manager->set_unit_permissions($permissions['unitId'], $permissions['userId'], $permissions['isAdmin']));
});


$app->delete('/permissions/channel', function () use ($manager){
	$permissions = json_decode(file_get_contents("php://input"), true);
	echo json_encode($manager->remove_unit_permissions($permissions['unitId'], $permissions['userId']));
});

}