<?php
/** @var $db */
require_once "Includes/admin-auth.php";
require_once('Includes/Functions.php');
require_once "includes/connection.php";

// Haal reservation_id uit de GET-parameter
$reservation_id = $_GET['reservation_id'] ?? null;
$error = '';
$success = '';

// Haal gegevens van de afspraak op voor bevestiging, alleen als er geen POST-verzoek is geweest
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $query = "SELECT first_name, last_name, date_time, course FROM reservations WHERE reservation_id = ?";
    $stmt = mysqli_prepare($db, $query);

    if ($stmt === false) {
        $error = "Er is een fout opgetreden bij het voorbereiden van de query: " . mysqli_error($db);
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $reservation_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $appointment = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$appointment) {
            $error = "Afspraak niet gevonden.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        // Verwijdering van de afspraak
        $deleteQuery = "DELETE FROM reservations WHERE reservation_id = ?";
        $stmt = mysqli_prepare($db, $deleteQuery);

        if ($stmt === false) {
            $error = "Er is een fout opgetreden bij het voorbereiden van de query: " . mysqli_error($db);
        } else {
            mysqli_stmt_bind_param($stmt, 'i', $reservation_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $success = "De afspraak is succesvol verwijderd.";
                $appointment = null; // Leegmaken van appointment om duplicaatbericht te voorkomen
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van de afspraak.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "U moet de checkbox aanvinken om de afspraak te verwijderen.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style/admin-style.css">
    <title>Afspraak Verwijderen</title>
</head>
<body>
<?php include('Includes/header.php') ?>
<main>
    <p class="flex justify-center Danger" style="margin:5vh 5vw; font-size: var(--font-size-large)">Afspraak verwijderen</p>

    <?php if ($error): ?>
        <p class="flex justify-center" style="color: var(--colors-text)"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="flex justify-center" style="color: var(--colors-text)"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($appointment): ?>
        <p class="flex justify-center"  style="color: var(--colors-text); font-size: var(--font-size-big)">Bent u zeker dat u de volgende afspraak wilt verwijderen?</p>
        <ul>
            <li style="color: var(--colors-text); font-size: var(--font-size-big); margin: 0 10vw">Naam: <?= htmlspecialchars($appointment['first_name']) ?> <?= htmlspecialchars($appointment['last_name']) ?></li>
            <li style="color: var(--colors-text); font-size: var(--font-size-big); margin: 0 10vw">Datum en tijd: <?= htmlspecialchars(date('d-m-Y H:i', strtotime($appointment['date_time']))) ?></li>
            <li style="color: var(--colors-text); font-size: var(--font-size-big); margin: 0 10vw">Baan: <?= htmlspecialchars($appointment['course']) ?></li>
        </ul>
        <form class="column" method="post">
            <div class="row between" style="gap: 5px">
                <input required class="checkbox-small" type="checkbox" id="confirm_delete" name="confirm_delete">
                <label for="confirm_delete">Ja, ik wil deze afspraak verwijderen</label>
            </div>
            <button class="danger-button" type="submit">Afspraak Verwijderen</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
