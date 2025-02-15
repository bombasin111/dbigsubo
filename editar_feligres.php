<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM feligreses WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $feligres = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $ci = $_POST['ci'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $bautizo = isset($_POST['bautizo']) ? 1 : 0;
    $confirmacion = isset($_POST['confirmacion']) ? 1 : 0;
    $matrimonio = isset($_POST['matrimonio']) ? 1 : 0;
    $pag = $_POST['pag'] ?? null;

    $stmt = $conn->prepare("
        UPDATE feligreses
        SET nombre = :nombre, ci = :ci, fecha_nacimiento = :fecha_nacimiento,
            bautizo = :bautizo, confirmacion = :confirmacion, matrimonio = :matrimonio, pag = :pag
        WHERE id = :id
    ");

    $stmt->execute([
        'nombre' => $nombre,
        'ci' => $ci,
        'fecha_nacimiento' => $fecha_nacimiento,
        'bautizo' => $bautizo,
        'confirmacion' => $confirmacion,
        'matrimonio' => $matrimonio,
        'pag' => $pag,
        'id' => $id
    ]);

    // Recuperar iglesia_id después de la actualización
    $stmt = $conn->prepare("SELECT iglesia_id FROM feligreses WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $feligres = $stmt->fetch();

    if (empty($feligres['iglesia_id'])) {
        die("Error: iglesia_id no está definido.");
    }

    // Redirigir usando JavaScript
    echo "<script>window.location.href = 'iglesia.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Feligrés</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Modificar Feligrés</h1>
    <form method="POST" class="form-dos-filas">
        <input type="hidden" name="id" value="<?php echo $feligres['id']; ?>">
        
        <!-- Primera fila: Nombre -->
        <div class="fila">
            <input type="text" name="nombre" value="<?php echo $feligres['nombre']; ?>" placeholder="Nombre" required>
        </div>

        <!-- Segunda fila: CI, Fecha de Nacimiento, Checkboxes y Botón -->
        <div class="fila">
            <input type="number" name="ci" value="<?php echo $feligres['ci']; ?>" placeholder="Cédula (opcional)">
            <input type="date" name="fecha_nacimiento" value="<?php echo $feligres['fecha_nacimiento']; ?>">
            <label>
                <input type="checkbox" name="bautizo" value="1" <?php echo $feligres['bautizo'] ? 'checked' : ''; ?>> Bautizado
            </label>
            <label>
                <input type="checkbox" name="confirmacion" value="1" <?php echo $feligres['confirmacion'] ? 'checked' : ''; ?>> Confirmación
            </label>
            <label>
                <input type="checkbox" name="matrimonio" value="1" <?php echo $feligres['matrimonio'] ? 'checked' : ''; ?>> Matrimonio
            </label>
            <input type="number" name="pag" value="<?php echo $feligres['pag']; ?>" placeholder="Página (opcional)">
            <button type="submit">Guardar Cambios</button>
        </div>
    </form>
</body>
</html>