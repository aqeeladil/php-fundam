<?php
require_once "config.php";
require_once "User.php";

$user = new User();
$user->logout();

header("Location: login.php");
exit;
