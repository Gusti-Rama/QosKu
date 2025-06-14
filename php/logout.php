<?php
session_start();
session_unset();
session_destroy();

setcookie('username', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');

header("Location: ../pages/login.php");
exit;
?>