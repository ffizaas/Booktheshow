<?php
session_start();

// HAPUS SEMUA DATA SESSION
$_SESSION = [];
session_destroy();

header("Location: login.php");
exit();
?>