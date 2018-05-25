
<?php

include("../php/db.php");
$Database = new Database();

#var_dump(md5(rand(0,1000)));

echo "<br>";

echo "<a href='http://localhost/link/cfee398643cbc3dc5eefc89334cacdc1' target='_blank'>Link</a>";

echo "<br><pre>";

#var_dump($Database->createRandomChat());


$chatId = 1;
$userId = 5;
$message = "akfjödfkjs dfjsakfjsdfajs fs";
#var_dump($Database->writeMessage($chatId, $userId, $message));


/*
foreach ($Database->getAllMessagesFromChat(41) as $key => $value) {
	echo "<br>------<br>$key<br>";

	foreach ($value as $k => $v) {
		//echo "$k<br>";
		$message = $v['message'];
		$timeadded = $v['timeadded'];
		$date = $v['date'];
		$time = $v['time'];
		$datediff = $v['datediff'];
		$name = $v['name'];
		echo "<b>$name: $message</b><br>\t ($timeadded \t $datediff \t $date \t $time)<br>";
	}
}
*/

//https://code.tutsplus.com/tutorials/how-to-implement-email-verification-for-new-members--net-3824

?>