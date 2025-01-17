<?php
if (isset($_POST['submit'])) {
    /** @var mysqli $db */
    require_once "includes/connection.php";
    $errors = [];
    if (isset($_POST['submit'])) {
        $firstName = $_POST['firstName'];
        if ($firstName === '') {
            $errors['firstName'] = "Vul a.u.b. Uw voornaam in.";
        }
        $lastName = $_POST['lastName'];
        if ($lastName === '') {
            $errors['lastName'] = "Vul a.u.b. Uw achternaam in.";
        }
        $email = $_POST['email'];
        if ($email === '') {
            $errors['email'] = "Vul a.u.b. Uw e-mail in.";
        }
        $password = $_POST['password'];
        if ($password === '') {
            $errors['password'] = "Maak a.u.b. een wachtwoord aan.";
        }
        if (empty($errors)) {
            $query = "SELECT user_id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors['email'] = "Dit e-mailadres is al in gebruik.";
            }
            mysqli_stmt_close($stmt);
        }
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 'ssss', $firstName, $lastName, $email, $hashedPassword);
            $result = mysqli_stmt_execute($stmt);
            if ($result) {
                header('Location: login.php?email=' . urlencode($email));
                exit;
            } else {
                // Foutmelding als de query mislukt
                $errors['query'] = "Er is iets mis gegaan bij het registreren.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section>
    <form action="" method="post">
        <div class="column" style="width: 500px">
            <p class="title">Registreer</p>
            <div class="form-column"> <!-- Voornaam -->
                <div>
                    <label class="label" for="firstName">Voornaam</label>
                </div>
                <div>
                    <input class="input" id="firstName" type="text" name="firstName"
                           value="<?= htmlspecialchars($firstName ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
                </div>
                <p class="Danger">
                    <?= $errors['firstName'] ?? '' ?>
                </p>
            </div>
            <div class="form-column"> <!-- Achternaam -->
                <div>
                    <label class="label" for="lastName">Achternaam</label>
                </div>
                <div>
                    <input class="input" id="lastName" type="text" name="lastName"
                           value="<?= htmlspecialchars($lastName ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
                </div>
                <p class="Danger">
                    <?= $errors['lastName'] ?? '' ?>
                </p>
            </div>
            <div class="form-column"> <!-- Email -->
                <div>
                    <label class="label" for="email">E-mailadres</label>
                </div>
                <div>
                    <input class="input" id="email" type="email" name="email"
                           value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
                </div>
                <p class="Danger">
                    <?= $errors['email'] ?? '' ?>
                </p>
            </div>
            <div class="form-column"> <!-- Wachtwoord -->
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
            <!-- Submit -->
            <button class="link-button" type="submit" name="submit">Registreer</button>
        </div>
    </form>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>