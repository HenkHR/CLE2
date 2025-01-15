<?php
require_once "Includes/admin-auth.php";
require_once('Includes/Functions.php');
/** @var $db */
require_once "includes/connection.php";

// Datums kloppend maken en ophalen
$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$timeslot = $_GET['timeslot'];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$date = "$year-$month-$day $timeslots[$timeslot]";

$query = "SELECT reservation_id, first_name, last_name, email, phone_number, course FROM reservations WHERE date_time = '$date'";
$result = mysqli_query($db, $query);

$reservations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservations[] = $row;
    }
} else {
    // Log de fout of toon een foutmelding
    echo "Er is een fout opgetreden bij het ophalen van de reserveringen.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/admin-style.css">
    <title>Reserveringen beheren</title>
</head>
<body>
<?php include('Includes/header.php') ?>
<main>
    <div class="timeslot" style="font-size: var(--font-size-bigger)"><?= date('d F Y', strtotime($date)) ?> om <?= date('H:i', strtotime($date)) ?></div>
    <section class="baanoverzicht">
        <?php
        // Vooraf ingevulde banen
        $banen = [
            1 => '',
            2 => '',
            3 => ''
        ];
        foreach ($reservations as $klant) {
            $baanNummer = $klant['course'];
            $reservation_id = $klant['reservation_id'];
            if (isset($banen[$baanNummer])) {
                $banen[$baanNummer] = "
                    <article class='side-bordered column'>
                        <div class='baannummer'>Baan $baanNummer</div>
                        <div class='klantinfo'>
                            " . htmlspecialchars($klant['first_name']) . " " . htmlspecialchars($klant['last_name']) . "<br>
                            " . htmlspecialchars($klant['email']) . "<br>
                            " . htmlspecialchars($klant['phone_number']) . "
                        </div>
                        <div class='afspraak-wijzigen'>
                            <a class='afspraak-details' href='reservering-wijzigen.php?timeslot=$timeslot&day=$day&month=$month&year=$year&course=$baanNummer'>Afspraak wijzigen</a>
                            <a class='Danger' href='reservering-verwijderen.php?reservation_id=$reservation_id'>Afspraak verwijderen</a>
                        </div>            
                    </article>";
            }
        }
        for ($i = 1; $i <= 3; $i++) {
            if (empty($banen[$i])) {
                echo "
            <article class='side-bordered column'>
                <div class='baannummer'>Baan $i</div>
                <div class='niet-verhuurd'>Niet verhuurd</div>
            </article>";
            } else {
                echo $banen[$i];
            }
        }
        ?>
    </section>
</main>
</body>
</html>
