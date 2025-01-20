<?php
/** @var $db */
require_once "includes/admin-auth.php";
require_once('Includes/connection.php');
$today = time() + 3600;
$date = date('Y-m-d H:i', $today);
$updateSucces = $updateError = "";
$id = $_GET['id'];
$current_url = $_SERVER['PHP_SELF'];
$query = "SELECT user_id, first_name, last_name, email, phone_number, is_admin FROM users WHERE user_id = $id";
$result = mysqli_query($db, $query);
$user = mysqli_fetch_assoc($result);
if (isset($_POST['submit'])) {
    $firstName = mysqli_real_escape_string($db, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($db, $_POST['last_name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phoneNumber = !empty($_POST['phone_number']) ? mysqli_real_escape_string($db, $_POST['phone_number']) : "NULL";
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $updateError = 'Voornaam, Achternaam en E-mailadres moeten ingevuld zijn.';
    }
    if (!empty($_POST['phone_number']) && !preg_match('/^\+?[0-9]{9,15}$/', $_POST['phone_number'])) {
        $updateError = 'Het telefoonnummer is ongeldig. Vul een geldig telefoonnummer in of laat het invulveld leeg.';
    }
    if (empty($updateError)) {
        $update_query = "UPDATE users
                         SET first_name = '$firstName', last_name = '$lastName', email = '$email', phone_number = $phoneNumber
                         WHERE user_id = $id";
        if (mysqli_query($db, $update_query)) {
            setcookie('update_message', 'Gebruikersgegevens bijgewerkt.', time() + 1, "/");
            header("Location: $current_url?id= $id");
            exit;
        } else {
            $updateError = "Fout bij het bijwerken van gebruikersgegevens.";
        }
    }
}
if (isset($_COOKIE['update_message'])) {
    $updateSucces = $_COOKIE['update_message'];
} else {
    $updateSucces = '';
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
<?php include('Includes/header.php') ?>
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
<section>
    <form action="" method="post">
        <div class="column" style="width: 500px">
            <p class="subtitle">Account van <br><?= $user['first_name'] . ' ' . $user['last_name']?></p>
            <div class="form-column"> <!-- Voornaam -->
                <div>
                    <label class="label" for="firstName">Voornaam</label>
                </div>
                <div>
                    <input class="input" id="firstName" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>"/>
                </div>
            </div>
            <div class="form-column"> <!-- Achternaam -->
                <div>
                    <label class="label" for="lastName">Achternaam</label>
                </div>
                <div>
                    <input class="input" id="lastName" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>"/>
                </div>
            </div>
            <div class="form-column"> <!-- Email -->
                <div>
                    <label class="label" for="email">E-mailadres</label>
                </div>
                <div>
                    <input class="input" id="email" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"/>
                </div>
            </div>
            <div class="form-column"> <!-- Telefoonnummer -->
                <div>
                    <label class="label" for="phoneNumber">Telefoonnummer <span style="color: var(--colors-text-footer); font-size: var(--font-size-small)">(optioneel)</span></label>
                </div>
                <div>
                    <input class="input" id="phoneNumber" type="text" maxlength="10" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"/>
                </div>
                <p class="Danger" style="margin-top: 25px">
                    <?= htmlspecialchars($updateError) ?>
                </p>
            </div>
            <!-- Submit -->
            <button type="submit" name="submit">Wijzigen</button>
        </div>
    </form>
    <form action="admin-account-verwijderen.php" method="post">
        <input type="hidden" name="userId" id="userId" value="<?=$id?>">
        <input type="submit" class="delete-account" value="Account verwijderen" style="background-color: #0F0F0F">
    </form>
</section>
<section class="userReservations">
    <p style="font-size: var(--font-size-bigger); font-weight: bold; margin-top: 50px; margin-bottom: 15px">Gemaakte reserveringen</p>
    <?php if(count($userReservations) > 0){?>
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
                <?php foreach($userReservations as $reservation): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($reservation['date_time'])) ?></td>
                        <td style="background-color: var(--colors-background-lighter)"><?= date('H:i', strtotime($reservation['date_time'])) ?></td>
                        <td><?= htmlspecialchars($reservation['course']) ?></td>
                        <?php if ($today < strtotime($reservation['date_time'])): ?>
                            <td class="dltButton">
                                <a class='Danger' href='annuleren.php?reservation_id=<?= $reservation['reservation_id'] ?>'>Annuleer</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php } if(count($pastUserReservations) > 0){?>
        <p style="margin-top: 50px; color: var(--colors-text-footer)">Voorgaande reserveringen</p>
        <div class="reservationsTable">
            <table>
                <thead>
                <tr>
                    <th style="color: var(--colors-text-footer)">Datum</th>
                    <th style="color: var(--colors-text-footer); background-color: var(--colors-background-lighter)">Tijd</th>
                    <th style="color: var(--colors-text-footer)">Baan</th>
                </tr>
                </thead>
                <tbody>
                <?php  foreach($pastUserReservations as $index => $reservation){ ?>
                    <tr>
                        <td style="color: var(--colors-text-footer)"><?= date('d-m-Y',strtotime($reservation['date_time']))?></td>
                        <td style="color: var(--colors-text-footer); background-color: var(--colors-background-lighter)"><?= date('H:i',strtotime($reservation['date_time'])) ?></td>
                        <td style="color: var(--colors-text-footer)"><?= $reservation['course']?></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
    <?php } else{ ?>
        <p> Deze gebruiker heeft nog geen reserveringen gemaakt</p>
    <?php } ?>
</section>
<?php include('Includes/footer.php') ?>
</body>
</html>