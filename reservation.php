<?php
$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$timeslot = $_GET['timeslot'];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$date = "$year-$month-$day $timeslots[$timeslot]";
//echo date('d-m-Y H:i', strtotime($date));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="reservation.css">
    <title>Reserveren</title>
</head>
<body>
<header>

</header>
<main>
    <div class="reservation-overzicht"> U reserveert voor <?= date('d-m-Y', strtotime($date)) ?> om <?= date('H:i', strtotime($date)) ?></div>
    <section class="reservation-form">
        <form action="">
            <div class="forminput"></div>
        </form>
    </section>
</main>
<footer>

</footer>
</body>
</html>