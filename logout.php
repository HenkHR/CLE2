<?php
// Start die sessie
session_start();
// Vernietig vernietig die sessie
session_unset();
session_destroy();
// Terug naar de landingspagina
header("Location: index.php");
// Stop met die code.
exit();
?>