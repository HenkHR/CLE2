<?php
/** @var mysqli $db */
require_once('includes/auth.php');
require_once('includes/connection.php');
print_r($_POST);
if(isset($_POST['userId']) || isset($_GET['userId']))
{
    $id = $_POST['userId'] ?? $_GET['userId'];
    $query = "SELECT * FROM users WHERE user_id = $id";
    $result = mysqli_query($db, $query);

    if (!$result) {
        die('Error ' . mysqli_error($db));
    }
    $user = mysqli_fetch_assoc($result);
    if (!$user) {
        die('Gebruiker niet gevonden.');
    }
    if (isset($_POST['submit'])) {
        if (!isset($_POST['checkbox'])) {
            echo '<p class="help is-danger">Je moet bevestigen dat je je account wilt verwijderen.</p>';
        } else {
            $deleteQuery = "DELETE FROM users WHERE user_id = $id";
            mysqli_query($db, $deleteQuery);
                setcookie('update_message', 'Account verwijderd', time() + 1, "/");
                header("Location: account-deleted-succes.php");
                exit();
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
    <title>Account verwijderen - Focus Health & Fitness</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<?php include('includes/header.php') ?>
<section>
    <form action="?userId=<?=$id?>" method="post">
        <div class="column" style="width: 500px">
            <p class="subtitle Danger">Account verwijderen</p>
            <div class="row between">
                <label class="checkbox" for="checkbox">Weet je zeker dat je het account van <?= $user['first_name'] . ' ' . $user['last_name']?> wilt verwijderen?</label>
                <input required class="checkbox-small" type="checkbox" id="checkbox" name="checkbox"/>
            </div>
            <!-- Verwijderen -->
            <button class="danger-button" type="submit" name="submit" value="">Account verwijderen</button>
        </div>
    </form>
</section>
<?php include('includes/footer.php') ?>
</body>
</html>