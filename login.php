<?php

/**  @var $db */

session_start();
require_once('includes/connection.php');
$login = false;
$errors = [];
$email = $_GET['email'] ?? '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '') {
        $errors['email'] = 'Vul een e-mailadres in.';
    }
    if ($password === '') {
        $errors['password'] = 'Vul een wachtwoord in.';
    }

    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) !== 1) {
            $errors['email'] = 'E-mailadres niet gevonden.';
        } elseif ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'];

                $login = true;

                // Verkrijg de redirect URL uit de querystring (standaard naar index.php als niet ingesteld)

                $redirect = $_GET['redirect'] ?? 'index.php';
                header("Location: $redirect");
                exit();
            } else {
                $errors['loginFailed'] = 'Wachtwoord is onjuist.';
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
            <p class="title">Log in</p>
            <div class="form-column">
                <label class="label" for="email">E-mail</label>
                <div>
                    <input class="input" id="email" type="text" name="email" value="<?= $email ?? '' ?>"/>
                </div>
                <p class="Danger">
                    <?= $errors['email'] ?? '' ?>
                </p>
            </div>

            <div class="form-column">
                <label class="label" for="password">Wachtwoord</label>
                <div class="column">
                    <input class="input" id="password" type="password" name="password"/>
                    <?php if (isset($errors['loginFailed'])) { ?>
                        <div class="Danger">
                            <?= $errors['loginFailed'] ?>
                        </div>
                    <?php } ?>
                </div>
                <p class="Danger">
                    <?= $errors['password'] ?? '' ?>
                </p>
            </div>
            <button type="submit" name="submit">Inloggen</button>
        </div>
    </form>
    <div>
        <a href="wachtwoord-vergeten.php">wachtwoord vergeten?</a>
    </div>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>