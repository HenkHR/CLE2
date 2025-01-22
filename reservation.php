<?php
session_start();
/** @var $db */
require_once('includes/connection.php');
require_once('includes/functions.php');
$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$selectedWeek = $_GET['week'];
$timeslot = $_GET['timeslot'];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
$date = "$year-$month-$day $timeslots[$timeslot]";
// Haal alle reserveringen op voor het specifieke tijdslot
$query = "SELECT course FROM reservations WHERE date_time = '$date'";
$result = mysqli_query($db, $query);
$occupiedCourses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $occupiedCourses[] = $row['course'];
}
$today = time();
if (strtotime($date) > strtotime('+1 month', $today))
{
    header('Location: calendar.php?week='. $selectedWeek);
}
if (strtotime($date) < $today) {
    header('Location: calendar.php?week='. $selectedWeek);}
if ($timeslot > 7) {
    header('Location: calendar.php?week='. $selectedWeek);}

// Zoek de eerste beschikbare baan
$totalCourses = 3; // Aantal banen, pas aan als er meer of minder banen zijn
$courseID = null;
for ($i = 1; $i <= $totalCourses; $i++) { // die geinige for loop
    if (!in_array($i, $occupiedCourses)) { // Als de baan nog niet verhuurd is
        $courseID = $i; // Baan ID wordt toegewezen
        break; // Stop met die loop
    }
}

// Als er geen beschikbare baan is, terug naar de kalender (beveiliging)
if ($courseID === null) {
    header('Location: calendar.php');
    exit;
}

$user = null;
$userId = null;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT first_name, last_name, email, phone_number FROM users WHERE user_id = $userId";
    $userResult = mysqli_query($db, $userQuery);
    $user = mysqli_fetch_assoc($userResult);
}

if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $userData = "$firstName $lastName $email $phoneNumber";

    $errors = [
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'phoneNumber' => ''
    ];

    if ($firstName == '') {
        $errors['firstName'] = "Voer hier uw voornaam in";
    }
    if ($lastName == '') {
        $errors['lastName'] = "Voer hier uw achternaam in";
    }
    if ($email == '') {
        $errors['email'] = "Voer hier uw email-adres in";
    }
    if (empty($phoneNumber) || !preg_match('/^\+?[0-9]{9,15}$/', $phoneNumber)) {
        $errors['phone_number'] = "Vul een geldig telefoonnummer in.";
    }

    if ($errors['firstName'] == '' && $errors['lastName'] == '' && $errors['email'] == '' && $errors['phone_number'] == '') {
        if ($userId !== null) {
            $query = "INSERT INTO reservations (user_id, date_time, first_name, last_name, email, phone_number, course) 
                      VALUES('$userId', '$date', '$firstName', '$lastName', '$email', '$phoneNumber', '$courseID')";
        } else {
            $query = "INSERT INTO reservations (date_time, first_name, last_name, email, phone_number, course)
                      VALUES('$date', '$firstName', '$lastName', '$email', '$phoneNumber', '$courseID')";
        }
        mysqli_query($db, $query);

        if ($user && !$user['phone_number']) {
            $updatePhoneQuery = "UPDATE users SET phone_number = '$phoneNumber' WHERE user_id = $userId";
            mysqli_query($db, $updatePhoneQuery);
        }

        mysqli_close($db);

        //De confirmation mail verzenden
        $to = $email;
        $subject = 'bevestiging padelbaan reservering';
        $fullMessage = 'Beste ' . $firstName . ' ' . $lastName . "\n" . 'Bedankt voor uw reservering:' . "\n" . date('d F Y', strtotime($date)) . "\n" . date('H:i', strtotime($date)) . "\n" . 'Baan ' . $courseID;
        mail($to, $subject, $fullMessage);

        header('Location: confirmation.php?date='.$date.'&course='.$courseID.'&firstName='.$firstName.'&lastName='.$lastName.'&email='.$email.'&phoneNumber='.$phoneNumber.'');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/reservation.css">
    <title>Reserveren</title>
</head>
<body style="background-color: var(--colors-background)">
<?php include('includes/header.php') ?>
<main>
    <div class="reservation-overzicht"> U reserveert voor <?= date('d F Y', strtotime($date)) ?>
        om <?= date('H:i', strtotime($date)) ?> voor baan <?= $courseID ?></div>
    <section class="reservation-form">
        <form class="column" action="" method="post">
            <?php if (!isset($_SESSION['user_id'])) { ?>
                <div class="formInput column">
                    <label for="firstName">Voornaam</label>
                    <input class="input" id="firstName" type="text" maxlength="30" name="firstName"
                           value="<?= htmlspecialchars($_POST['firstName'] ?? $user['first_name'] ?? '') ?>"/>
                </div>
                <p style="color: var(--colors-text)"><?= $errors['firstName'] ?? '' ?></p>
                <div class="formInput column">
                    <label for="lastName">Achternaam</label>
                    <input class="input" id="lastName" type="text" maxlength="30" name="lastName"
                           value="<?= htmlspecialchars($_POST['lastName'] ?? $user['last_name'] ?? '') ?>"/>
                </div>
                <p style="color: var(--colors-text)"><?= $errors['lastName'] ?? '' ?></p>
                <div class="formInput column">
                    <label for="email">E-mailadres</label>
                    <input class="input" id="email" type="email" maxlength="30" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? '') ?>"/>
                </div>
                <p style="color: var(--colors-text)"><?= $errors['email'] ?? '' ?></p>
            <?php } else { ?>
                <input type="hidden" id="firstName" name="firstName" value="<?= $_SESSION['first_name'] ?>">
                <input type="hidden" id="lastName" name="lastName" value="<?= $_SESSION['last_name'] ?>">
                <input type="hidden" id="email" name="email" value="<?= $_SESSION['email'] ?>">
            <?php } ?>
            <?php if (empty($user['phone_number']) || isset($_POST['phoneNumber'])) { ?>
                <div class="formInput column">
                    <label for="phoneNumber">Telefoonnummer</label>
                    <input class="input" id="phoneNumber" type="tel" maxlength="15" name="phoneNumber"
                           value="<?= htmlspecialchars($_POST['phoneNumber'] ?? $user['phone_number'] ?? '') ?>"/>
                </div>
                <p style="color: var(--colors-text)"><?= $errors['phone_number'] ?? '' ?></p>
            <?php } else { ?>
                <input type="hidden" id="phoneNumber" name="phoneNumber" value="<?= $user['phone_number'] ?>">
            <?php } ?>
            <button class="submitButton" type="submit" name="submit">Reserveer</button>
        </form>

    </section>
</main>
<?php include('includes/footer.php') ?>
</body>
</html>