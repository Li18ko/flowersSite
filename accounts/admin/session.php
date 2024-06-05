<?php
session_start();
$session_admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : false;

if (!$session_admin) {
    header("Location: ../../admin.html");
    exit();
}
?>
