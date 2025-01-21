<?php
session_start();
$date = $_GET['date'];
$course = $_GET['course'];
$phoneNumber = $_GET['phone_number'];
$firstname = $_SESSION['first_name'];
$lastname = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afspraak gemaakt - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section class="flex column" style="margin: 5vh 5vw 0">
    <p class="justify-center text-center" style="color: var(--colors-text); font-size: var(--font-size-bigger); font-weight: bold">Bedankt voor uw reservering!</p>
    <div class="reservationsTable justify-center" style="margin: 3vh auto 15px auto; font-size: var(--font-size-medium)">
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th style="background-color: var(--colors-background-lighter)">E-mail</th>
                    <th>Telefoonnummer</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $firstname ?> <?= $lastname ?></td>
                    <td style="background-color: var(--colors-background-lighter)"><?= $email ?></td>
                    <td><?= $phoneNumber ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="reservationsTable justify-center" style="margin: 15px auto 3vh auto; font-size: var(--font-size-medium)">
        <table>
            <thead>
            <tr>
                <th>Datum</th>
                <th style="background-color: var(--colors-background-lighter)">Tijd</th>
                <th>Baannummer</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= date('d F Y', strtotime($date)) ?></td>
                    <td style="background-color: var(--colors-background-lighter)"><?= date('H:i', strtotime($date)) ?></td>
                    <td>Baan <?= $course ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row justify-center" style="margin-bottom: 5vh; gap: 3vw">
        <a class="redirect-button" href="index.php">Terug naar de thuispagina</a>
        <?php if (isset($_SESSION['user_id'])) { ?>
        <a class="redirect-button" href="profile.php">Bekijk uw afspraken</a>
        <?php } ?>
    </div>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>