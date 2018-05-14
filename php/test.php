
<pre>

<?php
include("db.php");
$Database = new Database();


/* Nachrichten ausgeben im Chat juledaniel */
var_dump($Database->getAllMessagesFromChat_v2(2));



/* Test-Nachricht von Julia an Daniel */

$chatId = 2;
$userId = 5;
$message = "hallo ich bin ein test";

// var_dump($Database->writeMessage_v2($chatId, $userId, $message));

?>

</pre>
