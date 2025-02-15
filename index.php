<?php
// Datos de ejemplo: lista de iglesias
$iglesias = [
    "San Pedro",
    "Santiago",
    "La Merced",
    "Parroquia de Poroma",
    "Stma. Trinidad",
    "Santo Domingo",
    "Catedral, St. Guadalupe",
    "Sagrada Familia",
    "Cristo Rey",
    "San Clemente",
    "San Fco. Solano",
    "San Francisco",
    "San José",
    "San Juan de Dios",
    "San Lázaro",
    "San Matías",
    "San Miguel",
    "San Roque",
    "Santa Ana",
    "Santa Rosa",
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecciona una Iglesia</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <h1>Bienvenido</h1>
    <p>Por favor, selecciona una iglesia:</p>

    <form action="login.php" method="POST">
        <label for="iglesias">Selecciona una iglesia:</label>
        <select name="iglesias" id="iglesias" required>
            <?php foreach ($iglesias as $iglesia): ?>
                <option value="<?php echo htmlspecialchars($iglesia); ?>"><?php echo htmlspecialchars($iglesia); ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Continuar</button>
    </form>
</body>
</html>