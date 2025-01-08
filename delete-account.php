<?php
/** @var mysqli $db */
require_once('auth.php');
require_once('includes/connection.php');
$id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die('Error ' . mysqli_error($db));
}
$user = mysqli_fetch_assoc($result);
if (!$user) {
    die('Gebruiker niet gevonden.');
}
if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    if ($password === '') {
        $errors['password'] = "Vul a.u.b Uw wachtwoord in.";
    } else {
        if (!isset($_POST['checkbox'])) {
            echo '<p class="help is-danger">Je moet bevestigen dat je je account wilt verwijderen.</p>';
        } else {
            $deleteQuery = "DELETE FROM users WHERE user_id = ?";
            $deleteStmt = mysqli_prepare($db, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, 'i', $id);
            mysqli_stmt_execute($deleteStmt);
            session_unset();
            session_destroy();
            if (mysqli_stmt_affected_rows($deleteStmt) > 0) {
                header("Location: index.php");
                exit();
            } else {
                echo '<p class="help is-danger">Fout bij het verwijderen van je account.</p>';
            }
        }
    }
}
mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account verwijderen - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <?php include('Includes/header.php') ?>
    <section>
        <form action="" method="post">
            <div class="column" style="width: 500px">
                <p class="subtitle Danger">Account verwijderen</p>
                <p class="Danger" style="margin: 10px 0 40px 0">Vul je wachtwoord opnieuw in om je account te kunnen verwijderen.</p>
                <div class="form-column"> <!-- Wachtwoord invullen voordat je kan verwijderen -->
                    <div>
                        <label class="label" for="password">Wachtwoord</label>
                    </div>
                    <div>
                        <input class="input" id="password" type="password" name="password"/>
                    </div>
                    <p class="Danger">
                        <?= $errors['password'] ?? '' ?>
                    </p>
                </div>
                <div class="row between">
                    <label class="checkbox" for="checkbox">Weet je zeker dat je je account wilt verwijderen?</label>
                    <input required class="checkbox-small" type="checkbox" id="checkbox" name="checkbox"/>
                </div>
                <!-- Verwijderen -->
                <button class="danger-button" type="submit" name="submit">Account verwijderen</button>
            </div>
        </form>
    </section>
    <?php include('Includes/footer.php') ?>
</body>
</html>
