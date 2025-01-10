<?php

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact</title>
</head>
<body>
<nav>
    <!-- include nav -->
</nav>
<main>
    <section> <!-- kan ook als een div in de footer -->
        <form action=""> <!-- verzend de mail -->
            <label for="email">email</label>
            <input type="email" id="email" name="email" required value="<?= $_SESSION['email'] ?? "" ?>">

            <label for="firstname">Voornaam</label>
            <input type="text" id="firstname" name="firstname" required value="<?= $_SESSION['first_name'] ?? "" ?>">
            <label for="lastname">Achternaam</label>
            <input type="text" id="lastname" name="lastname" required value="<?= $_SESSION['last_name'] ?? "" ?>">

            <label for="message">bericht</label>
            <input type="text" id="message" name="message" required>
            <button type="submit" value="submit">Verstuur</button>
        </form>
    </section>
</main>
<footer>
    <!-- include footer -->
</footer>
</body>
</html>