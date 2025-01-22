<?php
/** @var $db */
require_once('includes/auth.php');
require_once('includes/connection.php');
$today = time() + 3600;
$date = date('Y-m-d H:i', $today);
$updateSucces = $updateError = "";
$id = $_SESSION['user_id'];
$query = "SELECT user_id, first_name, last_name, email, phone_number, is_admin FROM users WHERE user_id = $id";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
$updateError = ['firstName' => '', 'lastName' => '', 'email' => '', 'phone_number' => ''];
if (isset($_POST['submit'])) {
    $firstName = mysqli_real_escape_string($db, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($db, $_POST['last_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($db, $_POST['phone_number']) ?? '';

    $hasError = false; // Controleer of er fouten zijn

    if (empty($firstName)) {
        $updateError['firstName'] = "Vul a.u.b uw voornaam in.";
        $hasError = true;
    } else {
        $update_query = "UPDATE users SET first_name = '$firstName' WHERE user_id = $id"; // Zet gelijk de nieuw ingevoerde voornaam in de database, ookal zijn er op andere plekken fouten gemaakt.
        mysqli_query($db, $update_query);
        $user['first_name'] = $firstName;
        $_SESSION['first_name'] = $firstName;
    }

    if (empty($lastName)) {
        $updateError['lastName'] = "Vul a.u.b. uw achternaam in.";
        $hasError = true;
    } else {
        $update_query = "UPDATE users SET last_name = '$lastName' WHERE user_id = $id"; // Zelfde hier
        mysqli_query($db, $update_query);
        $user['last_name'] = $lastName;
        $_SESSION['last_name'] = $lastName;
    }

    if (empty($email)) {
        $updateError['email'] = "Vul a.u.b. uw email in.";
        $hasError = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // https://www.w3schools.com/php/func_filter_var.asp
        $updateError['email'] = "Vul een geldig e-mailadres in.";
        $hasError = true;

    } else {

        // We gaan controleren of het e-mailadres al bestaat in de database
        $email_check_query = "SELECT user_id FROM users WHERE email = '$email' AND user_id != $id";
        $email_check_result = mysqli_query($db, $email_check_query);

        // Als er meer dan 0 resultaten komen
        if (mysqli_num_rows($email_check_result) > 0) {
            $updateError['email'] = "Dit e-mailadres is al in gebruik.";
            $hasError = true;
        } else {
            // Update het e-mailadres als het uniek is
            $update_query = "UPDATE users SET email = '$email' WHERE user_id = $id";
            mysqli_query($db, $update_query);
            $user['email'] = $email;
            $_SESSION['email'] = $email;
        }
    }

    if (!empty($phoneNumber) && !preg_match('/^\+?[0-9]{9,15}$/', $phoneNumber)) {
        $updateError['phone_number'] = "Het telefoonnummer is ongeldig. Vul een geldig telefoonnummer in of laat het invulveld leeg.";
        $hasError = true;
    } else{
        $update_query = "UPDATE users SET phone_number = '$phoneNumber' WHERE user_id = $id";
        mysqli_query($db, $update_query);
        $user['phone_number'] = $phoneNumber;
    }
}
$userReservations = [];
$userReservations_query = "SELECT * FROM reservations WHERE user_id = '$id' AND date_time >= '$date' ORDER BY date_time ASC";
$userReservationsResult = mysqli_query($db, $userReservations_query);
while ($row = mysqli_fetch_assoc($userReservationsResult)) {
    $userReservations[] = $row;
}
$pastUserReservations = [];
$pastUserReservations_query = "SELECT * FROM reservations WHERE user_id = '$id' AND date_time < '$date' ORDER BY date_time DESC";
$pastUserReservationsResult = mysqli_query($db, $pastUserReservations_query);
while ($row = mysqli_fetch_assoc($pastUserReservationsResult)) {
    $pastUserReservations[] = $row;
}
mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section class="logout">
    <a class="logout-button" href="logout.php">Uitloggen<span
                style="display:block; width: 25px; height: 25px; margin-left: 5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                     class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                    <path fill-rule="evenodd"
                          d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/></svg></span>
    </a>
</section>
<p class="flex justify-center" style="color: var(--colors-text); font-size: var(--font-size-very-large); font-weight: bold">Account</p>
<div class="flex profile-row">

    <section class="userData overflow-hidden">
        <form action="" method="post">
            <div class="column">
                <div class="update-cookie">
                    <p class="cookie-text"><?= htmlspecialchars($updateSucces) ?></p>
                </div>
                <p class="title">Gegevens</p>
                <div class="form-column"> <!-- Voornaam -->
                    <div>
                        <label class="label" for="firstName">Voornaam</label>
                    </div>
                    <div>
                        <input class="input" id="firstName" type="text" name="first_name"
                               value="<?= htmlspecialchars($user['first_name']) ?>"/>
                    </div>
                </div>
                <p class="Danger">
                    <?= htmlspecialchars($updateError['firstName']) ?>
                </p>
                <div class="form-column"> <!-- Achternaam -->
                    <div>
                        <label class="label" for="lastName">Achternaam</label>
                    </div>
                    <div>
                        <input class="input" id="lastName" type="text" name="last_name"
                               value="<?= htmlspecialchars($user['last_name']) ?>"/>
                    </div>
                </div>
                <p class="Danger">
                    <?= htmlspecialchars($updateError['lastName']) ?>
                </p>
                <div class="form-column"> <!-- Email -->
                    <div>
                        <label class="label" for="email">E-mailadres</label>
                    </div>
                    <div>
                        <input class="input" id="email" type="email" name="email"
                               value="<?= htmlspecialchars($user['email']) ?>"/>
                    </div>
                </div>
                <p class="Danger">
                    <?= htmlspecialchars($updateError['email']) ?>
                </p>
                <div class="form-column"> <!-- Telefoonnummer -->
                    <div>
                        <label class="label" for="phoneNumber">Telefoonnummer <span
                                    style="color: var(--colors-text-footer); font-size: var(--font-size-small)">(optioneel)</span></label>
                    </div>
                    <div>
                        <input class="input" id="phoneNumber" type="text" maxlength="10" name="phone_number"
                               value="<?php if(empty($user['phone_number'])){ ?><?php }else{ echo  $user['phone_number'];}?>"/>
                    </div>
                    <p class="Danger">
                        <?= htmlspecialchars($updateError['phone_number']) ?>
                    </p>
                </div>
                <!-- Submit -->
                <button type="submit" name="submit">Wijzigen</button>
                <a class="delete-account" style="margin-bottom: 5vh" href="delete-account.php">Account verwijderen</a>
            </div>
        </form>
    </section>

    <section class="userReservations overflow-hidden">
        <p class="title">Reserveringen</p>
        <?php if (count($userReservations) > 0) { ?>
            <p style="margin-bottom: 10px; font-size: var(--font-size-big); font-weight: bold">Komende reserveringen</p>
            <div class="reservationsTable" style="font-size: var(--font-size-medium)">
                <table>
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th style="background-color: var(--colors-background-lighter)">Tijd</th>
                        <th>Baan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($userReservations as $reservation): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($reservation['date_time'])) ?></td>
                            <td style="background-color: var(--colors-background-lighter)"><?= date('H:i', strtotime($reservation['date_time'])) ?></td>
                            <td><?= htmlspecialchars($reservation['course']) ?></td>
                            <?php if ($today < strtotime($reservation['date_time'])): ?>
                                <td class="dltButton">
                                    <a class='Danger'
                                       href='annuleren.php?reservation_id=<?= $reservation['reservation_id'] ?>'>Annuleer</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p> U heeft nog geen nieuwe reserveringen</p>
        <?php }
        if (count($pastUserReservations) > 0) { ?>
            <p style="margin-top: 50px; color: var(--colors-text-footer)">Voorgaande reserveringen</p>
            <div class="reservationsTable" style="font-size: var(--font-size-small)">
                <table>
                    <thead>
                    <tr>
                        <th style="color: var(--colors-text-footer)">Datum</th>
                        <th style="color: var(--colors-text-footer); background-color: var(--colors-background-lighter)">
                            Tijd
                        </th>
                        <th style="color: var(--colors-text-footer)">Baan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pastUserReservations as $index => $reservation) { ?>
                        <tr>
                            <td style="color: var(--colors-text-footer)"><?= date('d-m-Y', strtotime($reservation['date_time'])) ?></td>
                            <td style="color: var(--colors-text-footer); background-color: var(--colors-background-lighter)"><?= date('H:i', strtotime($reservation['date_time'])) ?></td>
                            <td style="color: var(--colors-text-footer)"><?= $reservation['course'] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>
</div>
<?php include('includes/footer.php') ?>
</body>
</html>