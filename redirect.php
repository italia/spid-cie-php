<?php
$entityBody = file_get_contents('php://input');

echo "<b>BODY:</b>" . $entityBody . "<br>";
echo "\"<html>
<br>
<body>
<a href=\"/proxy-spid.php?client_id=60ed55efc7d7a&action=logout\">Esci | Logout</a>
</body>
</html>";
