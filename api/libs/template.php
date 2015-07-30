<?php
include_once '../common.php';
include_once '../libraries/unit.php';

$template = json_decode(file_get_contents("php://input"), true);

if (isset($template)) {
	if (isset($template['deleted']) && $template['deleted']) {
		deleteTemplate($template['id']);
	} else {
		createTemplate($template);
	}
}
fetchTemplates();

function createTemplate($template) {
	createLayer($template, TEMPLATE_ID());
}

function deleteTemplate($id) {
	deleteLayer(intval($id));
}

function fetchTemplates() {
	echo '{"result":' . json_encode(getLayers(TEMPLATE_ID())) . '}';
}

function TEMPLATE_ID() {return -1;}
?>