<?php
require_once "includes/functions.php";
require_once "includes/connection.php";

$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$columnID = 0;
$rowID = 0;
$selectedWeek = $_GET['week'] ?? 0;


$timestampWeek = strtotime("+$selectedWeek weeks");


$weekDays = getWeekDays($timestampWeek);
$monthOfWeek = date('F', $weekDays[0]['timestamp']);

$yearOfWeek = date('Y', $weekDays[0]['timestamp']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="reservation.css">
    <title>Weekoverzicht</title>
</head>
<body>
<header></header>
<main>
    <div class="title">
        <a href="?week=<?= $selectedWeek - 1 ?>">Vorige week</a>
        <span><?= $monthOfWeek . ' ' . $yearOfWeek; ?><br> Week: <?= date("W", $timestampWeek)?></span>
        <a href="?week=<?= $selectedWeek + 1 ?>">Volgende week</a>
    </div>
    <div class="days">

        <?php foreach ($weekDays as $weekday) { ?>
            <div class="dayOfWeek">
                <?= $weekday['day'] . ' ' . $weekday['dayNumber'] . ' ' . $weekday['month']; ?>
            </div>
        <?php } ?>
    </div>

    <section class="calendar">
        <div class="time-column">
            <?php for($y=0; $y<count($timeslots); $y++){?>
                <span class="timeslot"><?= $timeslots[$y] ?></span>
            <?php }?>
        </div>
        <?php for($x=0; $x<7; $x++){ ?>
            <div class="column">
            <?php for($i=0; $i < count($timeslots)-1; $i++){
                ?>
                <a class="calendar-button" href="reservation.php?timeslot=<?=$rowID?>&day=<?=date("d", $weekDays[$columnID]['timestamp'])?>&year=<?=date("Y", $weekDays[$columnID]['timestamp'])?>&month=<?= date('m', $weekDays[$columnID]['timestamp'])?>">
                    <?= $days[$columnID] ?>
                    <?= $timeslots[$rowID]?>
                    <?= date('Y',$weekDays[$columnID]['timestamp'])?>
                </a>
            <?php
            $rowID++;
            if ($rowID > 7)
            {
                $rowID = 0;
            }
            }?>
            </div>
        <?php
            $columnID++;
        } ?>
    </section>

</main>
<footer></footer>
</body>
</html>

