<?php
require_once('system/library.php');

session_destroy();
header('Location: login.php');
exit;
