<?php
session_start();
require_once "db.php";

$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;
?>