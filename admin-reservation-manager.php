<?php
require_once "includes/admin-auth.php";
require_once('includes/functions.php');
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
<?php include('includes/header.php') ?>
<main>
    <div class="previous-page">
        <a class="previous-page-button" href="admin-calendar.php">
            <span style="display:block; width: 25px; height: 25px; margin-left: 5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                     class="bi bi-caret-left" viewBox="0 0 16 16">
                    <path d="M10 12.796V3.204L4.519 8zm-.659.753-5.48-4.796a1 1 0 0 1 0-1.506l5.48-4.796A1 1 0 0 1 11 3.204v9.592a1 1 0 0 1-1.659.753"/>
                </svg>
            </span>
            Terug
        </a>
    </div>
    <div class="timeslot" style="font-size: var(--font-size-bigger)"><?= date('d F Y', strtotime($date)) ?>
        om <?= date('H:i', strtotime($date)) ?></div>
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
                            <a class='Danger' href='reservering-verwijderen.php?reservation_id=$reservation_id&timeslot=$timeslot&day=$day&month=$month&year=$year'>Afspraak verwijderen</a>
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
