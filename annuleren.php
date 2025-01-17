<?php
/** @var $db */
require_once "Includes/auth.php";
require_once "includes/connection.php";
$error = '';
$success = '';
$reservation_id = $_GET['reservation_id'] ?? null;
$user_id = $_SESSION['user_id']; // Zorg ervoor dat je sessie correct gestart is en user_id ingesteld is

if (!$reservation_id) {
    $error = "Geen geldige afspraak geselecteerd.";
} else {
    $query = "SELECT first_name, last_name, date_time, course FROM reservations WHERE reservation_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($db, $query);

    if ($stmt === false) {
        $error = "Er is een fout opgetreden bij het voorbereiden van de query: " . mysqli_error($db);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $reservation_id, $user_id); // Bind zowel reservation_id als user_id
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $appointment = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$appointment) {
            $error = "U heeft geen toestemming om deze afspraak te bekijken.";
        }
    }
}

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
    <title>Afspraak Annuleren</title>
</head>
<body>
<?php include('Includes/header.php') ?>
<div class="previous-page">
    <a class="previous-page-button" href="profile.php">
            <span style="display:block; width: 25px; height: 25px; margin-left: 5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-caret-left" viewBox="0 0 16 16">
                    <path d="M10 12.796V3.204L4.519 8zm-.659.753-5.48-4.796a1 1 0 0 1 0-1.506l5.48-4.796A1 1 0 0 1 11 3.204v9.592a1 1 0 0 1-1.659.753"/>
                </svg>
            </span>
        Terug
    </a>
</div>
<p class="flex justify-center Danger" style="margin:5vh 5vw; font-size: var(--font-size-large)">Afspraak Annuleren</p>

<?php if ($error): ?>
    <p class="flex justify-center" style="color: var(--colors-text)"><?= htmlspecialchars($error) ?></p>
<?php elseif ($success): ?>
    <p class="flex justify-center" style="color: var(--colors-text)"><?= htmlspecialchars($success) ?></p>
<?php elseif ($appointment): ?>
    <p class="flex justify-center"  style="color: var(--colors-text); font-size: var(--font-size-big)">Bent u zeker dat u uw afspraak op <?= htmlspecialchars(date('d-m-Y H:i', strtotime($appointment['date_time']))) ?> op baan <?= htmlspecialchars($appointment['course']) ?> wilt annuleren?</p>
    <form class="column" method="post">
        <div class="row between" style="gap: 5px">
            <input required class="checkbox-small" type="checkbox" id="confirm_delete" name="confirm_delete">
            <label for="confirm_delete">Ja, ik wil deze afspraak verwijderen</label>
        </div>
        <button class="danger-button" type="submit">Afspraak Verwijderen</button>
    </form>
<?php endif; ?>
</body>
</html>
