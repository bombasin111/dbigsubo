<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM feligreses WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: iglesia.php");
    exit();
}
?>