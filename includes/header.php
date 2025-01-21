<header>
    <nav class="navbar">
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="index.php">Homepage</a>
            <a href="admin-calendar.php">Reserveringoverzicht</a>
            <a href="admin-account-manager.php">Gebruikers beheren</a>
            <a href="logout.php">Uitloggen</a>
        <?php else: ?>
            <a href="about.php">Over Focus</a>
            <a href="info.php">Info Padel</a>
            <a class="logo" href="../home.php">
                <img class="navbar-logo" src="/images/focus_1.png" alt="">
            </a>
            <a href="calendar.php">Reserveren</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Account</a>
            <?php else: ?>
                <a href="login.php">Inloggen</a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</header>