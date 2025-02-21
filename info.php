<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Padel</title>
    <link rel="stylesheet" href="style/info.css">
</head>
<body>
<?php include('includes/header.php') ?>
<main>
    <p class="title" style="margin-bottom: 5vh">Focus padel</p>
    <div class="info" style="color: var(--colors-text)">
        <div class="leftItem">
            <h2>Wat is padel</h2>
            <p>Padel is een racket- en balsport die gespeeld wordt in een afgesloten veld of kooi. Het wordt
                voornamelijk met vier spelers gespeeld, waarbij een bal die lijkt op een tennisbal, met een racket over
                een net gespeeld wordt. Het lijkt een mix van tennis en squash, maar het speelveld is ongeveer 25%
                kleiner dan een tennisveld.</p>
            <a class="webButton" href="https://www.nlpadel.nl/" target="_blank">Lees meer</a>
        </div>
        <div class="center-item">
            <img src="images/Constructie_padel.jpg" alt="">
        </div>
        <div class="rightItem">
            <h2>Padel bij Focus</h2>
            <p>Daar is het dan eindelijk, je had misschien al het een en ander gehoord over het bouwen van een padelbaan
                maar we hebben nu eindeijk de vergunningen binnen!
                Sinds December is de bouw in volle gang, de planning is dat in eind Februari/begin Maart de eerste
                balletjes geslagen kunnen worden.
            </p>
            <a class="webButton" href="calendar.php">Reserveer nu</a>
        </div>
    </div>
    <section class="QnA">

    </section>
</main>
</body>
<?php include('includes/footer.php') ?>
</html>

