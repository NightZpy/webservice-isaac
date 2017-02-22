<?php

require "gump.class.php";
require "Database.class.php";
if (isset($_GET['disable'])) {
	$isValid = GUMP::is_valid($_GET, array(
	    'id' => 'required|integer',
	    'disable' => 'required|integer'
	));

	if ($isValid) {
		$id = $_GET['id'];
		$disable = $_GET['disable'];
		$strQuery = "UPDATE clients SET enabled = $disable WHERE id = $id";
		$db = new Database('data/clients.db');
		$result = $db->query($strQuery);
		$db->close();
		if (!$result)
			echo json_encode(['success' => false, 'message' => "Ha habido un error!"]);
		else 
			echo json_encode(['success' => true, 'message' => "El cliente ha sido actualizado exitosamente!"]);		
	} else {
		echo json_encode(['success' => false, 'message' => "Cliente '$name' no ha podido ser actualizado!"]);
	}

} elseif (isset($_GET['name']) && !empty($_GET['name'])) {
	$isValid = GUMP::is_valid($_GET, array(
	    'name' => 'required|alpha_numeric'
	));

	if ($isValid) {
		$name = $_GET['name'];
		$strQuery = "INSERT INTO clients (name) VALUES ('$name')";
		$db = new Database('data/clients.db');
		$result = $db->query($strQuery);
		$db->close();
		if (!$result)
			echo json_encode(['success' => false, 'message' => "Ha habido un error!"]);
		else 
			echo json_encode(['success' => true, 'message' => "Cliente '$name' ha sido creado exitosamente!"]);
	} else {
		echo json_encode(['success' => false, 'message' => "Cliente '$name' no ha podido agregarse a la base de datos!"]);
	}	
} else {
	echo json_encode(['success' => false, 'message' => "Ha habido un error!"]);
}





