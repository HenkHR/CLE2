<?php
// Start the session.
session_start();
// destroy the session.
session_unset();
session_destroy();
// Redirect to login page
header("Location: index.php");
// Exit the code.
exit();
?>