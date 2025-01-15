<?php
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <link rel="stylesheet" href="style/admin-account-dashboard.css">
</head>
<body>
<?php include('Includes/header.php') ?>

<section class="search-bar">
    <form action="search.php" method="get">
        <input type="text" name="query" placeholder="Zoeken..." required>
        <button type="submit">Zoeken</button>
    </form>
</section>

<section class="dashboard-section">
    <div class="text-content">
        <h2></h2>
        <p></p>
    </div>
    <div class="text-content">
        <h2>Contact Informatie</h2>
        <p>Adres: Middelharnis, Oude-Tonge en Ouddorp.</p>
    </div>
</section>

<?php include('Includes/footer.php') ?>
</body>
</html>
