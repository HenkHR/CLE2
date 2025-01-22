<?php
/** @var $db */
session_start(); // Start een sessie om gegevens tijdelijk op te slaan

require_once('includes/connection.php'); // Verbind met de database

$step = '0'; // Standaard stap
$errors = []; // Array voor fouten

if (isset($_POST['submit'])) {
    $step = $_POST['step'];
    $email = $_POST['email'];

    if ($step === '0') { // Stap 0: Controleer e-mail en genereer code
        if (empty($email)) {
            $errors['email'] = 'Vul een e-mailadres in.';
        } else {
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) !== 1) {
                $errors['email'] = 'E-mailadres niet gevonden.';
            }

            if (empty($errors)) {
                // Genereer een willekeurige code van 10 cijfers
                $code = '';
                for ($i = 0; $i < 10; $i++) {
                    $code .= random_int(0, 9);
                }
                // Sla de code op in de sessie
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_email'] = $email;

                // Verstuur de code per e-mail
                $to = $email;
                $subject = 'Wachtwoord reset code';
                $message = 'Uw code is: ' . $code;
                mail($to, $subject, $message);

                $step = '1'; // Ga naar de volgende stap
            }
        }
    }

    if ($step === '1') { // Stap 1: Controleer de ingevoerde code
        if (isset($_POST['code'])) {
            $entered_code = $_POST['code'];
            if ($entered_code === $_SESSION['reset_code']) {
                $step = '2';
            } else {
                $errors['code'] = 'De code komt niet overeen.';
            }
        }
    }

    if ($step === '2') { // Stap 2: Reset het wachtwoord
        if (isset($_POST['password'], $_POST['repeatPassword'])) {
            $password = $_POST['password'];
            $repeatPassword = $_POST['repeatPassword'];

            if ($password !== $repeatPassword) {
                $errors['password'] = 'Wachtwoorden komen niet overeen.';
            }

            if (empty($password)) {
                $errors['password'] = 'Vul een wachtwoord in.';
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET password = ? WHERE email = ?";
                $stmt = mysqli_prepare($db, $query);
                mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $_SESSION['reset_email']);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    session_unset(); // Wis de sessiegegevens
                    session_destroy(); // Vernietig de sessie
                    mysqli_close($db);
                    header('Location: login.php?email=' . urlencode($email));
                    exit;
                } else {
                    $errors['password'] = 'Het resetten van het wachtwoord is mislukt. Neem contact op.';
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section>
    <div class="register">
        <a class="register-button" href="register.php">Nog geen account? Registreer hier!</a>
    </div>
    <form class="column" style="margin: 25px 0 75px 0" action="" method="post">
        <div class="column" style="width: 500px">
            <div class="form-column">
                <?php if ($step === '0') { ?>
                    <label class="label" for="email">E-mail</label>
                    <input class="input" id="email" type="email" name="email" value="<?= $email ?? '' ?>"/>
                    <p class="Danger">
                        <?= $errors['email'] ?? '' ?>
                    </p>

                    <input type="hidden" name="step" value="0">
                    <button type="submit" name="submit">Mail verzenden</button>
                <?php }
                if ($step === '1') { ?>
                    <label class="label" for="code">code</label>
                    <input class="input" type="text" name="code" id="code">
                    <p class="Danger">
                        <?= $errors['code'] ?? '' ?>
                    </p>
                    <input type="hidden" name="email" value="<?= $email ?>">
                    <input type="hidden" name="step" value="1">
                    <button type="submit" name="submit">Verder</button>
                <?php }
                if ($step === '2') { ?>
                    <label class="label" for="password">Nieuw wachtwoord</label>
                    <input class="input" type="password" name="password" id="password">
                    <label class="label" for="repeatPassword">Herhaal wachtwoord</label>
                    <input class="input" type="password" name="repeatPassword" id="repeatPassword">
                    <p class="Danger">
                        <?= $errors['password'] ?? '' ?>
                    </p>
                    <input type="hidden" name="email" value="<?= $email ?>">
                    <input type="hidden" name="step" value="2">
                    <button type="submit" name="submit">Bevestig het nieuwe wachtwoord</button>
                <?php } ?>
            </div>
        </div>
    </form>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>
