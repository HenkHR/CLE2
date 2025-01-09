<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $name = isset($_SESSION['first_name']);
    $id = $_SESSION['user_id'];
}
if (isset($_SESSION['user_id'])) {
    $redirect = $_GET['user_id'] ?? null;
    $allowed_pages = ['profile.php', 'delete-account.php'];
    if ($redirect && in_array($redirect, $allowed_pages)) {
        header("Location: $redirect");
        exit();
    }
}
if (isset($_COOKIE['update_message'])) {
    $updateSucces = $_COOKIE['update_message'];
} else {
    $updateSucces = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padel - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <?php include('Includes/header.php') ?>
    <section class="title-section" style="background-image: url('/Images/FOCUS-MH-min.png'); background-size: cover; overflow: hidden">
        <p class="title">
            Padel
        </p>
    </section>
    <section class="main-section">
        <article>
            <p>artikel 1</p>
        </article>
        <img src="" alt="Padelbaan foto">
        <article>
            <p>artikel 2</p>
        </article>
    </section>
    <?php include('Includes/footer.php') ?>
</body>
</html>