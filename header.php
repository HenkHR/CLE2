<header>
    <nav class="navbar">
        <a href="about.php">Over Focus</a>
        <a href="info.php">Info Padel</a>
        <a class="logo" href="index.php">
            <img class="navbar-logo" src="/Images/focus_1.png" alt="">
        </a>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="adminreservationsplaceholder.php">Reserveringen beheren</a>
        <?php else: ?>
            <a href="reservation.php">Reserveren</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Account</a>
        <?php else: ?>
            <a href="login.php">Inloggen</a>
        <?php endif; ?>
    </nav>
</header>