<?php
require_once('admin/system/library.php');

session_destroy();
header('Location: index.php');
exit;
