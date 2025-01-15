<?php
if (isset($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);
    echo "You searched for: " . $query;
    // database logic moet hier
} else {
    echo "No search query provided.";
}
?>
