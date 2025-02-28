<?php
require_once "includes/functions.php";
/** @var mysqli $db */
require_once "includes/connection.php";
$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);
$today = time();
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$columnID = 0;
$rowID = 0;
$selectedWeek = $_GET['week'] ?? 0;


$timestampWeek = strtotime("+$selectedWeek weeks");

$weekDays = getWeekDays($timestampWeek);
$monthOfWeek = date('F', $weekDays[0]['timestamp']);
$yearOfWeek = date('Y', $weekDays[0]['timestamp']);
$startDate = $weekDays[0]['fullDate'];
$endDate = date('Y-m-d', strtotime($weekDays[6]['fullDate'] . "+1 days"));
$query = "SELECT * FROM reservations WHERE date_time >= '$startDate' AND date_time <= '$endDate'";
$result = mysqli_query($db, $query);
$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/reservation.css">
    <title>Weekoverzicht</title>
</head>
<body style="background-color: var(--colors-background)">
<?php include('includes/header.php') ?>
<main>
    <p class="flex justify-center"
       style="color: var(--colors-text); font-size: var(--font-size-very-large); font-weight: bold">Reserveren</p>
    <div class="title" style="margin-top: 5vh">
        <a href="?week=<?= $selectedWeek - 1 ?>">Vorige week</a>
        <span>
            <a href="?week=<?= 0 ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-calendar" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                </svg>
            </a>
            <?= $monthOfWeek . ' ' . $yearOfWeek; ?><br> Week: <?= date("W", $timestampWeek) ?> <br> <?php if ($selectedWeek != 0) { ?>
            <?php } ?>
        </span>
        <a href="?week=<?= $selectedWeek + 1 ?>">Volgende week</a>
    </div>
    <div class="days">
        <?php foreach ($weekDays as $weekday) { ?>
            <div <?php if (date('Y-m-d', $weekday['timestamp']) == date('Y-m-d', $today)) { ?> class="currentDayOfWeek" <?php } else { ?>class="dayOfWeek" <?php } ?>>
                <?= $weekday['day'] . ' ' . $weekday['dayNumber'] . ' ' . $weekday['month']; ?>
            </div>
        <?php } ?>
    </div>

    <section class="calendar">
        <div class="time-column">
            <?php for ($y = 0; $y < count($timeslots); $y++) { ?>
                <span class="timeslot"><?= $timeslots[$y] ?></span>
            <?php } ?>
        </div>
        <?php for ($x = 0; $x < 7; $x++) { ?>
            <div class="column">
                <?php for ($i = 0; $i < count($timeslots) - 1; $i++) {
                    ?>
                    <a <?php if (strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]) < $today || strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]) >= strtotime('+1 month', $today)
                       ) { ?>class="past-calender-button"
                        <?php } elseif (getReservationCount(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations) < 3
                        ) { ?> class="calendar-button"
                        <?php } else { ?> class="calendar-button-unavailable" <?php } ?>
                       href="reservation.php?timeslot=<?= $rowID ?>&day=<?= date("d", $weekDays[$columnID]['timestamp']) ?>&year=<?= date("Y", $weekDays[$columnID]['timestamp']) ?>&month=<?= date('m', $weekDays[$columnID]['timestamp']) ?>&week=<?= $selectedWeek ?>">
                        <strong><?= $timeslots[$rowID] . " - " . $timeslots[$rowID + 1]; ?></strong>
                        <br>
                        <?php if(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]) >= strtotime('+1 month', $today)) {?>
                            <p>Nog niet beschikbaar</p>
                        <?php } elseif(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]) < $today) {?>
                            <p>Niet meer beschikbaar</p>
                        <?php } elseif(getReservationCount(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations) > 2) { ?>
                            <p>Vol</p>
                        <?php } else { ?>
                        <p> Plekken beschikbaar: <?= getAvailableSpots(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations) ?> </p>
                        <?php } ?>
                        <div class="hiddenInfo">
                            <?php if (getReservationCount(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations) < 3 && strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]) > $today) { ?>
                                <div class="courses">
                                    <span <?php if (in_array("1", getCourseNumbersArray(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations))) { ?> class="takenCourse" <?php } else { ?> class="course"<?php } ?>> 1 </span>
                                    <span <?php if (in_array("2", getCourseNumbersArray(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations))) { ?> class="takenCourse" <?php } else { ?> class="course"<?php } ?>> 2 </span>
                                    <span <?php if (in_array("3", getCourseNumbersArray(strtotime(date('Y-m-d', $weekDays[$columnID]['timestamp']) . ' ' . $timeslots[$rowID]), $reservations))) { ?> class="takenCourse" <?php } else { ?> class="course"<?php } ?>> 3 </span>
                                </div>
                            <?php } ?>
                        </div>
                    </a>
                    <?php
                    $rowID++;
                    if ($rowID > 7) {
                        $rowID = 0;
                    }
                } ?>
            </div>
            <?php
            $columnID++;
        } ?>
    </section>
</main>
<?php include('includes/footer.php') ?>
</body>
</html>

