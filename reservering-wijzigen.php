<?php
/** @var $db */
require_once "Includes/admin-auth.php";
require_once('Includes/Functions.php');
require_once "includes/connection.php";

$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);

$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$timeslot = $_GET['timeslot'];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$date = "$year-$month-$day $timeslots[$timeslot]";
$course = $_GET['course'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCourse = $_POST['course'];
    $newDate = $_POST['date'];
    $newTimeslot = $_POST['timeslot'];
    $newDatetime = "$newDate $newTimeslot";

    if (!is_numeric($newCourse) || $newCourse < 1 || $newCourse > 3) {
        $error = "Kies baan nummer 1, 2, of 3.";
    } else {
        $availabilityQuery = "SELECT * FROM reservations WHERE date_time = '$newDatetime' AND course = '$newCourse'";
        $availabilityResult = mysqli_query($db, $availabilityQuery);

        if (mysqli_num_rows($availabilityResult) > 0) {
            $error = "De gekozen baan is al bezet voor dit tijdslot.";
        } else {
            $updateQuery = "UPDATE reservations SET course = '$newCourse', date_time = '$newDatetime' WHERE date_time = '$date' AND course = '$course'";
            mysqli_query($db, $updateQuery);

            // Nieuwe datum, maand en jaar voor de redirect
            $newYear = date('Y', strtotime($newDatetime));
            $newMonth = date('m', strtotime($newDatetime));
            $newDay = date('d', strtotime($newDatetime));
            $newTimeslotIndex = array_search($newTimeslot, $timeslots);

            // Redirect naar de nieuwe pagina
            header("Location: admin-reservation-manager.php?timeslot=$newTimeslotIndex&day=$newDay&month=$newMonth&year=$newYear");
            exit;
        }
    }
}

// Ophalen van klantgegevens
$query = "SELECT first_name, last_name, email, phone_number, course, date_time FROM reservations WHERE date_time = '$date' AND course = '$course'";
$result = mysqli_query($db, $query);
$klant = mysqli_fetch_assoc($result);
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
    <div class="timeslot" style="font-size: var(--font-size-bigger)">
        <?= htmlspecialchars($klant['first_name']) ?> <?= htmlspecialchars($klant['last_name']) ?> <br>
        <?= htmlspecialchars($klant['email']) ?> <br>
        <?= htmlspecialchars($klant['phone_number']) ?>
    </div>
    <div class="timeslot" style="font-size: var(--font-size-big)">
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="column" style="width: 500px">
                <div class="form-column">
                    <div>
                        <label class="label" for="course">Baan</label>
                    </div>
                    <div>
                        <input class="input" id="course" type="text" name="course" value="<?= htmlspecialchars($klant['course']) ?>"/>
                    </div>
                </div>
                <div class="form-column">
                    <div>
                        <label class="label" for="date">Datum</label>
                    </div>
                    <div>
                        <input class="input" id="date" type="date" name="date" value="<?= htmlspecialchars(date('Y-m-d', strtotime($klant['date_time']))) ?>"/>
                    </div>
                </div>
                <div class="form-column">
                    <div>
                        <label class="label" for="timeslot">Tijdslot</label>
                    </div>
                    <div class="flex justify-center">
                        <select class="input" style="width: 500px; padding: 5px" id="timeslot" name="timeslot">
                            <?php foreach ($timeslots as $slot): ?>
                                <option value="<?= $slot ?>" <?= $slot == date('H:i', strtotime($klant['date_time'])) ? 'selected' : '' ?>>
                                    <?= $slot ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button class="link-button" type="submit" name="submit">Wijzigen</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>