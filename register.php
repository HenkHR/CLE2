<?php
if (isset($_POST['submit'])) {
    /** @var mysqli $db */
    require_once "Includes/connection.php";
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
<header>
    <nav class="navbar">
        <a href="about.php">Over Focus</a>
        <a href="info.php">Info Padel</a>
        <a class="logo" href="index.php">
            <img class="navbar-logo" src="/Images/focus_1.png" alt="">
        </a>
        <a href="reservation.php">Reserveren</a>
        <a href="login.php">Inloggen</a>
    </nav>
</header>
<section>
    <form action="" method="post">
        <div class="column" style="width: 500px">
            <p class="title">Registreer</p>
            <div class="form-column"> <!-- Voornaam -->
                <div>
                    <label class="label" for="firstName">Voornaam</label>
                </div>
                <div>
                    <input class="input" id="firstName" type="text" name="firstName" value="<?= htmlspecialchars($firstName ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
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
                    <input class="input" id="lastName" type="text" name="lastName" value="<?= htmlspecialchars($lastName ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
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
                    <input class="input" id="email" type="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"/>
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
<footer class="row">
    <div class="footer-contact">
        <div class="row">
            <div class="footer-social-links">
                <a href="https://www.instagram.com/focushealthfitness/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg>
                </a>
                <a href="https://www.facebook.com/focushealthfitness/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                    </svg>
                </a>
            </div>
        </div>
        <p>
            <span style="color: var(--colors-link); font-size: var(--font-size-bigger)" >|</span> <span style="color: var(--colors-text); font-size: var(--font-size-bigger)">Middelharnis</span>
        </>
        <p>
            Industrieweg 38 <br>
            3241 MA Middelharnis <br>
            0187 - 64 12 24 (kies 2)
        </p>
    </div>
    <div class="footer-copyright">
        Copyright 2023 © Focus Health & Fitness | <a href="https://www.focushealthfitness.nl/privacyverklaring/">Privacyverklaring</a>
    </div>
    <div class="footer-location-contact">
        <div class="footer-info">
            <p>
                <span style="color: var(--colors-link); font-size: var(--font-size-bigger)">|</span> <span style="color: var(--colors-text); font-size: var(--font-size-bigger)">Oude-Tonge</span>
            </p>
            <p>
                Stationsweg 18 <br>
                3255 BL Oude-Tonge <br>
                0187 - 64 12 24 (kies 1)
            </p>
        </div>
        <p>
            <span style="color: var(--colors-link); font-size: var(--font-size-bigger)">|</span> <span style="color: var(--colors-text); font-size: var(--font-size-bigger)">Ouddorp</span>
        </p>
        <p>
            Boompjes 9 <br>
            3253 AC Ouddorp <br>
            0187 - 64 12 24 (kies 3)
        </p>
    </div>
</footer>
</body>
</html>