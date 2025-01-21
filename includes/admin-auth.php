<?php
session_start();
// We controleren even of de gebruiker admin is. Het zou niet heel fijn zijn als willekeurige mensen bij de bewerkingstools konden komen.
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}
?>