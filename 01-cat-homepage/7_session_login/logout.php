<h1>logout...</h1>
<?php
session_save_path('./');
session_start();
$_SESSION = array();
session_destroy();
header("Location: index.php");
exit();
?>
