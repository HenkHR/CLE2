<?php
//session starten voor autofill
session_start();
//datums kloppend maken en ophalen
$timezoneId = 'Europe/Amsterdam';
date_default_timezone_set($timezoneId);
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$timeslot = $_GET['timeslot'];
$timeslots = ['9:00', '10:30', '12:00', '13:30', '15:00', '16:30', '18:00', '19:30', '21:00'];
//deze date gaat in de database
$date = "$year-$month-$day $timeslots[$timeslot]";
//redirect als er al 3 reserveringen zijn, voorkomt deeplinken
/**  @var $db */
require_once('Includes/connection.php');
require_once('Includes/Functions.php');
$query = "SELECT * FROM reservations
            WHERE date_time = '$date'
            ";
$result = mysqli_query($db, $query);
$reservations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reservations[] = $row;
}
if (getReservationCount(strtotime($date), $reservations) > 2) {
    header('Location: calendar.php');
}
$courseIDs = 0;
foreach ($reservations as $reservation) {
    $courseIDs++;
}
$courseID = $courseIDs + 1;

//reserveren
if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];

    $errors = [
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'phoneNumber' => ''
    ];

    if ($_POST['firstName'] == '') {
        $errors['firstName'] = "Voer hier uw voornaam in";
    }
    if ($_POST['lastName'] == '') {
        $errors['lastName'] = "Voer hier uw achternaam in";
    }
    if ($_POST['email'] == '') {
        $errors['email'] = "Voer hier uw email-adres in";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Voer een geldig email-adres in";
    }
    //geen errors-> query bepalen, uitvoeren en db sluiten
    if ($errors['firstName'] == '' && $errors['lastName'] == '' && $errors['email'] == '' && $errors['phoneNumber'] == '') {
        if ($phoneNumber == '' || $phoneNumber == null) {
            $query = "INSERT INTO reservations (date_time, first_name, last_name, email, course)
                    VALUES('$date', '$firstName', '$lastName', '$email', '$courseID')";
        } else {
            $query = "INSERT INTO reservations (date_time, first_name, last_name, email, phone_number, course)
                        VALUES('$date', '$firstName', '$lastName', '$email', '$phoneNumber', '$courseID')";
        }

        mysqli_query($db, $query);
        mysqli_close($db);

        //De confirmation mail verzenden
        $to = $email;
        $subject = 'bevestiging padelbaan reservering';
        $fullMessage = 'Beste' . $firstName . $lastName . "\n" . 'Bedankt voor uw reservering:' . "\n" . date('d F Y', strtotime($date)) . "\n" . date('H:i', strtotime($date)) . "\n" . 'Baan' . $courseID;
        mail($to, $subject, $fullMessage);

        header('Location: confirmation.php');
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
<body>
<header>
    <!--?php include('Includes/header.php') ?-->
</header>
<main>
    <div class="reservation-overzicht"> U reserveert voor <?= date('d F Y', strtotime($date)) ?>
        om <?= date('H:i', strtotime($date)) ?> voor baan <?= $courseID ?></div>
    <section class="reservation-form">
        <form action="" method="post">

            <div class="formInput">
                <label class="label" for="firstName">Voornaam</label>
                <input class="input" id="firstName" type="text" maxlength="30" name="firstName"
                       value="<?= $_SESSION['first_name'] ?? $firstName ?? '' ?>"/>
            </div>
            <p>
                <?= $errors['firstName'] ?? '' ?>
            </p>
            <div class="formInput">
                <label class="label" for="lastName">Achternaam</label>
                <input class="input" id="lastName" type="text" maxlength="30" name="lastName"
                       value="<?= $_SESSION['last_name'] ?? $lastName ?? '' ?>"/>
            </div>
            <p>
                <?= $errors['lastName'] ?? '' ?>
            </p>
            <div class="formInput">
                <label class="label" for="email">Email-adres</label>
                <input class="input" id="email" type="email" maxlength="30" name="email"
                       value="<?= $_SESSION['email'] ?? $email ?? '' ?>"/>
            </div>
            <p>
                <?= $errors['email'] ?? '' ?>
            </p>
            <div class="formInput">
                <label class="label" for="phoneNumber">Telefoonnummer</label>
                <input class="input" id="phoneNumber" type="tel" maxlength="10" name="phoneNumber"
                       value=" <?= $_SESSION['phone_number'] ?? $phoneNumber ?? '' ?>"/>
            </div>
            <p>
                <?= $errors['phoneNumber'] ?? '' ?>
            </p>
            <button class="submitButton" type="submit" name="submit">Reserveer</button>
        </form>
    </section>
</main>
<footer>
    <!--?php include('Includes/footer.php') ?-->
</footer>
</body>
</html>