
<pre>

<?php
include("db.php");
$Database = new Database();


/* Nachrichten ausgeben im Chat juledaniel */
#var_dump($Database->getAllMessagesFromChat_v2(2));


foreach ($Database->getAllMessagesFromChat_v2(2) as $date => $messages) {
	echo "<h1>$date</h1>";
	
	foreach ($messages as $message) {
		#var_dump($message);

		$nachricht = $message['message'];
		$zeit = $message['time'];
		$name = $message['name'];
		echo "<div>$name: $nachricht ($zeit)</div>";
	}
}


/* Test-Nachricht von Julia an Daniel */

$chatId = 2;
$userId = 5;
$message = "hallo ich bin ein test";

// var_dump($Database->writeMessage_v2($chatId, $userId, $message));

?>

</pre>
