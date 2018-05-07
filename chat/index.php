<!DOCTYPE html>
<html lang="en">

<?php

include("../php/db.php");
$Database = new Database();

// prüfen, ob User eingeloggt ist
if(!$Database->isLoggedIn()) {
    // zur Login-Seite weiterleiten
    header('Location: /../');
}

?>

<head>
    <meta charset="UTF-8">
    <title>TextMe</title>
</head>
<body>
<!-- Hier den Code für die Main Page-->
<?php

?>
</body>
</html>
