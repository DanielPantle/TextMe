<?php

include("db.php");
$Database = new Database();

$result;

if(isset($_POST['method'])) {
	$method = $_POST['method'];
	
	if($method == "getAllChatsFromCurrentUser") {
		$result = $Database->getAllChatsFromCurrentUser();
	}
	else if($method == "getAllMessagesFromChat") {
		if(isset($_POST['chatId'])) {
			$result = $Database->getAllMessagesFromChat($_POST['chatId']);
		}
		else {
			$result = "Error: chatId missing";
		}
	}
	else if($method == "getCurrentUser") {
		$result = $Database->getCurrentUser();
	}
	else if($method == "logout") {
		$result = $Database->logout();
	}
	else {
		$result = "Error: method not valid";
	}
}
else {
	$result = "Error: method missing";
}

echo json_encode($result);

?>
