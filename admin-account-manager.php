<?php
/** @var $db */
require_once "includes/admin-auth.php";
require_once('includes/connection.php');

$users = null;
$error_message = null;
$success_message = null;
$limit = 20; // Aantal resultaten per pagina
$offset = 0; // Startpunt

// Standaardquery voor de eerste 20 gebruikers
$query = "SELECT * FROM users LIMIT ? OFFSET ?";
$stmt = $db->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error_message = "Geen gebruikers gevonden.";
}

$stmt->close();

// Controleer of "Load More" is gestuurd
if (isset($_POST['load_more'])) {
    $offset = intval($_POST['offset']);
}

// Gebruikers zoeken
if (isset($_POST['submit']) || isset($_POST['load_more'])) {
    $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '';

    // Dynamische query opbouwen
    $query = "SELECT * FROM users WHERE 1";
    $params = [];
    $types = "";

    if (!empty($first_name)) {
        $query .= " AND first_name LIKE ?";
        $params[] = "%" . $first_name . "%";
        $types .= "s";
    }
    if (!empty($last_name)) {
        $query .= " AND last_name LIKE ?";
        $params[] = "%" . $last_name . "%";
        $types .= "s";
    }

    // Voeg limiet en offset toe
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error_message = "Geen gebruikers gevonden.";
    }

    $stmt->close();
}

// Reservering annuleren
if (isset($_POST['cancel'])) {
    $users_id = intval($_POST['reservation_id']);
    $delete_query = "DELETE FROM reservations WHERE id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("i", $users_id);

    if ($stmt->execute()) {
        $success_message = "Reservering succesvol geannuleerd.";
    } else {
        $error_message = "Het annuleren van de reservering is mislukt.";
    }

    $stmt->close();
}
?>


<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gebruikers</title>
    <link rel="stylesheet" href="style/admin-account-dashboard.css">
</head>
<body>

<!-- Standaard header inladen -->
<?php include('includes/header.php') ?>

<section class="search-bar">
    <form action="" method="post">
        <input type="text" name="first_name" placeholder="Voornaam...">
        <input type="text" name="last_name" placeholder="Achternaam...">
        <button type="submit" name="submit">Zoeken</button>
    </form>
</section>

<section class="dashboard-section">
    <h2>Gebruikers</h2>
    <div class="text-content">
        <?php if ($success_message): ?>
            <p style="color: green;"><?= $success_message ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color: #f4f4f4;"><?= $error_message ?></p>
        <?php endif; ?>

        <?php if ($users): ?>
            <h3>Gegevens gevonden:</h3>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <a class="listButton" href="admin_user_profile.php?id=<?= htmlspecialchars($user['user_id']) ?>">
                            <strong>Naam: </strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>

            <!-- Load More knop -->
            <form action="" method="post">
                <input type="hidden" name="offset" value="<?= htmlspecialchars($offset + $limit) ?>">
                <button type="submit" name="load_more">Meer laden</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<!-- Standaard footer inladen -->
<?php include('includes/footer.php') ?>

</body>
</html>



