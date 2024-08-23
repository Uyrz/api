<?php
setcookie("token", "", time() - 3600,  "/", "", true, true);
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
