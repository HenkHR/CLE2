<?php
$host = "sql.hosted.hro.nl";
$database = "prj_2024_2025_cle2_t10";
$user = "prj_2024_2025_cle2_t10";
$password = "hausoong";

$db = mysqli_connect($host, $user, $password, $database)
or die("Error: " . mysqli_connect_error());