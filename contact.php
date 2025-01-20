<?php
//algemeen: autofill session-stuff
//check of de mail klopt
//check of het voor geen problemen zorgt met groepsgenoten
session_start();
//check submit en of het de juiste is
if (isset($_POST['submit'])) {
    // sinds ze required zijn zijn ze niet leeg
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    //email moet het juiste format hebben
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Geen geldig email format";
    }
    //als er geen errors zijn kan je de mail verzenden
    if (empty($error)) {
        $to = '1101595@hr.nl';
        $fullMessage = 'Van: ' . $_POST['firstName'] . ' ' . $_POST['lastName'] . "\n" . 'Email:' . $_POST['email'] . "\n" . $_POST['message'];

        mail($to, $subject, $fullMessage);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<header>
    <?php include('includes/header.php') ?>
</header>
<main>
    <section class="flex justify-center"> <!-- kan ook als een div in de footer -->
        <form class="flex column" style="width: 500px; margin-top: 5vh" action="" method="post"> <!-- post naar de pagina -->
            <!-- gebruiker's email-->
            <label class="label" for="email">E-mail</label>
            <input class="input" type="email" id="email" name="email" required
                   value="<?= $_SESSION['email'] ?? $email ?? '' ?>">
            <p class="danger" style="margin-bottom: 3vh">
                <?= $error['email'] ?? '' ?>
            </p>
            <!-- namen-->
            <label class="label" for="firstName">Voornaam</label>
            <input class="input" style="margin-bottom: 3vh; type="text" id="firstName" name="firstName" required
                   value="<?= $_SESSION['first_name'] ?? $firstName ?? '' ?>">
            <label class="label" for="lastName">Achternaam</label>
            <input class="input" style="margin-bottom: 3vh; type="text" id="lastName" name="lastName" required
                   value="<?= $_SESSION['last_name'] ?? $lastName ?? '' ?>">
            <!-- mail input-->
            <label class="label" for="subject">Onderwerp</label>
            <input class="input" style="margin-bottom: 3vh; type="text" id="subject" name="subject" required
                   value="<?= $subject ?? '' ?>">
            <label class="label" for="message">Bericht</label>
            <input class="input" style="margin-bottom: 1vh; type="text" id="message" name="message" required
                   value="<?= $message ?? '' ?>">
            <button style="margin-bottom: 5vh; type="submit" name="submit">Verstuur</button>
        </form>
    </section>
</main>
<?php include('includes/footer.php') ?>
</body>
</html>