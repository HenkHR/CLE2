<?php
$year = $_GET['year'];
$day = $_GET['columnID'];
$timeslot = $_GET['rowID'];
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];

echo "year=" . $year . ' - ' . "day=" . $days[$day] . ' - ' . "timeslot=" . $timeslots[$timeslot];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
    <title>Reserveren</title>
</head>
<body>

</body>
</html>