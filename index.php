<?php

require_once('extratorrent.php');
$ex = new extratorrent();

// handing POST requests only 
if(!empty($_POST)) {
	
	$type = isset($_POST['type']) && !empty($_POST['type']) ? $_POST['type'] : 'search';
	$query = isset($_POST['q']) && !empty($_POST['q']) ? $_POST['q'] : '';
	
	switch($type) { 
		
		case 'search': {
			
			$results = $ex->search($query);
			
		} break;
		case 'popular': {
			
			$results = $ex->popular();
			
		} break;
	}
	
	header('Content-Type: application/json;charset=utf-8');
	echo json_encode($results);
	die();
	
} else {

	die($_SERVER['REQUEST_METHOD'] . ' requests are not allowed');
}
