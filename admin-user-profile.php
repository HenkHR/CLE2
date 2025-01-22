<?php
/** @var $db */
require_once "includes/admin-auth.php";
require_once('includes/connection.php');
$today = time() + 3600;
$date = date('Y-m-d H:i', $today);
$updateSucces = $updateError = "";
$id = $_GET['id'];
$current_url = $_SERVER['PHP_SELF'];
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
        $updateError['firstName'] = "Vul a.u.b de voornaam in.";
        $hasError = true;
    } else {
        $update_query = "UPDATE users SET first_name = '$firstName' WHERE user_id = $id"; // Zet gelijk de nieuw ingevoerde voornaam in de database, ookal zijn er op andere plekken fouten gemaakt.
        mysqli_query($db, $update_query);
        $user['first_name'] = $firstName;
    }

    if (empty($lastName)) {
        $updateError['lastName'] = "Vul a.u.b. de achternaam in.";
        $hasError = true;
    } else {
        $update_query = "UPDATE users SET last_name = '$lastName' WHERE user_id = $id"; // Zelfde hier
        mysqli_query($db, $update_query);
        $user['last_name'] = $lastName;
    }

    if (empty($email)) {
        $updateError['email'] = "Vul a.u.b. het e-mailadres in.";
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
            $updateError['email'] = "Dit e-mailadres is al in gebruik door een andere gebruiker.";
            $hasError = true;
        } else {
            // Update het e-mailadres als het uniek is
            $update_query = "UPDATE users SET email = '$email' WHERE user_id = $id";
            mysqli_query($db, $update_query);
            $user['email'] = $email;
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
$userReservations_query = "SELECT * FROM reservations WHERE user_id = '$id' AND date_time >= '$date' ORDER BY date_time DESC";
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
<div class="previous-page">
    <a class="previous-page-button" style="display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--colors-text);
    padding: 5px;" href="admin-account-manager.php">
        <span style="display:block; width: 25px; height: 25px; margin-left: 5px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                 class="bi bi-caret-left" viewBox="0 0 16 16">
                <path d="M10 12.796V3.204L4.519 8zm-.659.753-5.48-4.796a1 1 0 0 1 0-1.506l5.48-4.796A1 1 0 0 1 11 3.204v9.592a1 1 0 0 1-1.659.753"/>
            </svg>
        </span>
        <span>Terug</span>
    </a>
</div>
<div class="update-cookie">
    <p class="cookie-text"><?= htmlspecialchars($updateSucces) ?></p>
</div>
<div class="row">
    <section class="column overflow-hidden" style="width: 50vw">
        <form action="" method="post">
            <div class="column" style="width: 500px">
                <p class="subtitle">Account van <br><?= $user['first_name'] . ' ' . $user['last_name'] ?></p>
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
                               value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"/>
                    </div>
                    <p class="Danger">
                        <?= htmlspecialchars($updateError['phone_number']) ?>
                    </p>
                </div>
                <!-- Submit -->
                <button type="submit" name="submit">Wijzigen</button>
            </div>
        </form>
        <form action="admin-account-verwijderen.php" method="post" style="margin-bottom: 5vh">
            <input type="hidden" name="userId" id="userId" value="<?= $id ?>">
            <input type="submit" class="delete-account" value="Account verwijderen" style="background-color: #0F0F0F">
        </form>
    </section>
    <section class="userReservations overflow-hidden" style="width: 50vw; border-left: 1px solid var(--colors-link)">
        <p class="subtitle">Gemaakte reserveringen</p>
        <?php if (count($userReservations) > 0) { ?>
            <p style="margin-bottom: 10px; font-size: var(--font-size-big); font-weight: bold">Komende reserveringen</p>
            <div class="reservationsTable" style="font-size: var(--font-size-big)">
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
        <?php }
        if (count($pastUserReservations) > 0) { ?>
            <p style="margin-top: 50px; color: var(--colors-text-footer)">Voorgaande reserveringen</p>
            <div class="reservationsTable">
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
        <?php } else { ?>
            <p> Deze gebruiker heeft nog geen reserveringen gemaakt</p>
        <?php } ?>
    </section>
</div>
<?php include('includes/footer.php') ?>
</body>
</html>