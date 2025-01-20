<?php
$code = 123456;
// db
require_once('includes/connection.php');
// zet de default voor de stap
$step = '0';
// als gepost
if (isset($_POST['submit'])) {
    $step = $_POST['step'];
    $email = $_POST['email'];

    // als step=0 (mail en code maken)
    if ($step === '0') {
        // klopt de mail en staat die in de db
        if ($email === '') {
            $errors['email'] = 'Vul een e-mailadres in.';
        } else {
            //db openen
            //query maken
            //query uitvoeren
            //staat email in query: verder
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) !== 1) {
                $errors['email'] = 'E-mailadres niet gevonden.';
            }

            if (empty($errors)) {
                // code aanmaken als stap 0 en email klopt
                $to = $email;
                $subject = 'wachtwoord reset code';
                $message = 'Uw code is' . "\n" . $code;
                mail($to, $subject, $message);

                // naar de volgende stap als alles is gelukt
                $step = '1';

            }
        }
    }
    // als step=1
    if ($step === '1') {
        if ($_POST['code'] === $code) {
            $errors['code'] = 'De code komt niet overeen';
        } else {
            $errors['code'] = 'eeeeehehhehehh goe guhdan';
            $step = '2';
        }
    }
    // als step=2
    if ($step === '2') {
        $newPassword = $_POST['newPassword'];
        $repeatPassword = $_POST['repeatPassword'];

        if ($_POST['newPassword'] !== $_POST['repeatPassword']) {
            $errors['password'] = 'Wachtwoord komt niet overeen';
        }
        if ($newPassword = '') {
            $errors['password'] = 'Vul een wachtwoord in';
        }
        if (empty($errors)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ?";
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 's', $hashedPassword);
            $result = mysqli_stmt_execute($stmt);
            //Zou goed zijn
            mysqli_close($db);
            header('Location: login.php?email=' . urlencode($email));

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
                    <label class="label" for="newPassword">Nieuw wachtwoord</label>
                    <input class="input" type="password" name="newPassword" id="newPassword">
                    <label class="label" for="repeatPassword">Herhaal wachtwoord</label>
                    <input class="input" type="password" name="repeatPassword" id="repeatPassword">
                    <p>
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
