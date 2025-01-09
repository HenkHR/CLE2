<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    // Als de gebruiker niet ingelogd is, doorverwijzen naar de loginpagina
    header("Location: login.php");
    exit();
}
?>