<?php 
	require "Database.class.php";
	$strQuery = "SELECT id, name, enabled FROM clients";
	$db = new Database('data/clients.db');
	$result = $db->query($strQuery);
	if ($result) {
		$data = [];
		while($row = $result->fetchArray(SQLITE3_ASSOC) )
			$data[] = [$row['id'], $row['name'], $row['enabled']];
		$db->close();
		$json = ['data' => $data];
		echo json_encode ( $json );
	}
	$db->close();