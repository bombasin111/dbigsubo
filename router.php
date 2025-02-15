<?php
session_start();
include 'db.php';

if (!isset($_SESSION['iglesia_id'])) {
    header("Location: login.php");
    exit();
}

$iglesia_id = $_SESSION['iglesia_id'];

// Obtener el nombre del archivo de la iglesia
$stmt = $conn->prepare("SELECT nombre_archivo FROM usuarios_iglesia WHERE iglesia_id = :iglesia_id");
$stmt->execute(['iglesia_id' => $iglesia_id]);
$iglesia = $stmt->fetch();

if ($iglesia && file_exists($iglesia['nombre_archivo'])) {
    include $iglesia['nombre_archivo'];
} else {
    echo "Página no encontrada.";
}
?>