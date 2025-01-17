<?php
/** @var $db */
require_once('includes/connection.php');

$user = null;
$error_message = null;
$phone_number = null;
// Controleer of de ID aanwezig is in de URL
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $phone_number = $user['phone_number'] ?? '';
    // Query om de specifieke gebruiker op te halen
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $error_message = "Gebruiker niet gevonden.";
    }

    $stmt->close();
} else {
    $error_message = "Geen geldige gebruiker geselecteerd.";
}
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profielpagina</title>
    <link rel="stylesheet" href="style/profile.css">
</head>
<body>

<!-- Standaard header inladen -->
<?php include('includes/header.php') ?>

<section class="profile-section">
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php elseif ($user): ?>
        <h2>Profiel van <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
        <p><strong>Voornaam:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
        <p><strong>Achternaam:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Telefoonnummer:</strong> <?= $phone_number ?></p>

    <?php endif; ?>
</section>

<!-- Standaard footer inladen -->
<?php include('includes/footer.php') ?>

</body>
</html>
