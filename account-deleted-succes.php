<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account verwijderd - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section class="column">
    <p class="Danger justify-center align-center text-center"
       style="margin: 50px 0 100px 0; font-size: var(--font-size-big)">
        Account verwijderd.
    </p>
    <a class="register-button element" style="width: 250px; margin-bottom: 100px" <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1){?> href="admin-account-manager.php" <?php }else{?>href="index.php" <?php } ?>>Terug naar de
        thuispagina</a>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>