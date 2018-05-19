<?php
session_start();
session_destroy();
include("./php/db.php");
$Database = new Database();
$Database->logout();
header('Location: ./index.php');
?>
