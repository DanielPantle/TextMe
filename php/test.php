
<?php

include("../php/db.php");
$Database = new Database();

#var_dump(md5(rand(0,1000)));

echo "<br>";

echo "<a href='http://localhost/link/cfee398643cbc3dc5eefc89334cacdc1' target='_blank'>Link</a>";

echo "<br><pre>";

var_dump($Database->createRandomChat());

#var_dump($Database->getAllMessagesFromChat(2));

//https://code.tutsplus.com/tutorials/how-to-implement-email-verification-for-new-members--net-3824

?>