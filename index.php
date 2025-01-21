<?php
session_start();
/** @var $db */
require_once('includes/connection.php');
$today = date('Y-m-d'); // Huidige datum, zonder tijd
if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];

    $userReservations = [];
// Selecteer alleen reserveringen van vandaag
    $userReservations_query = "SELECT * FROM reservations WHERE user_id = '$id' AND DATE(date_time) = '$today' ORDER BY date_time ASC";
    $userReservationsResult = mysqli_query($db, $userReservations_query);
    while ($row = mysqli_fetch_assoc($userReservationsResult)) {
        $userReservations[] = $row;
    }
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
<?php include('includes/header.php') ?>
<section class="title-section">
    <p class="title">
        <?php if (isset($_SESSION['user_id'])) { ?>
            Welkom, <?= $_SESSION['first_name'] ?>
        <?php } else { ?>
            Welkom bij Focus Padel!
        <?php } ?>
    </p>
    <p class="titleInfo">
        Op deze website vind je alles over padellen bij Focus
    </p>
</section>
<?php if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) != 1) { ?>
    <section class="reservation-section flex justify-center">
        <div class="column justify-center text-center">
            <p class="text-center"
               style="margin-bottom: 10px; color: var(--colors-text); font-size: var(--font-size-big); font-weight: bold">
                Reserveringen voor vandaag</p>
            <?php if (!empty($userReservations)) { ?>
                <div class="reservationsTable" style="margin: 3vh auto; font-size: var(--font-size-medium)">
                    <table>
                        <thead>
                        <tr>
                            <th style="background-color: var(--colors-background-lighter)">Tijd</th>
                            <th>Baan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($userReservations as $reservation): ?>
                            <tr>
                                <td style="background-color: var(--colors-background-lighter)"><?= date('H:i', strtotime($reservation['date_time'])) ?></td>
                                <td><?= htmlspecialchars($reservation['course']) ?></td>
                                <td class="dltButton">
                                    <a class='Danger'
                                       href='annuleren.php?reservation_id=<?= $reservation['reservation_id'] ?>'>Annuleer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <p class="text-center" style="color: var(--colors-text)">Je hebt vandaag geen reserveringen.</p>
                <a class="reservation-button text-center element" style="margin: 3vh auto;" href="calendar.php">Maak een
                    reservering</a>
            <?php } ?>
        </div>
    </section>
<?php }
if (!isset($_SESSION['is_admin']) != 1) { ?>
    <section class="main-section">
        <a href="info.php">Meer over padel</a>
        <a href="about.php">Meer over Focus</a>
        <?php if (!isset($_SESSION['user_id']) && (empty(($userReservations)))) { ?>
            <a href="calendar.php">Reserveren</a>
        <?php } ?>
    </section>
<?php } ?>
<?php include('includes/footer.php') ?>
</body>
</html>