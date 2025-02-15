<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['iglesia_id'])) {
    header("Location: login.php");
    exit();
}

$iglesia_id = $_SESSION['iglesia_id'];

// Obtener los datos de la iglesia
$stmt = $conn->prepare("SELECT * FROM iglesias WHERE id = :iglesia_id");
$stmt->execute(['iglesia_id' => $iglesia_id]);
$iglesia = $stmt->fetch();

if (!$iglesia) {
    die("Iglesia no encontrada.");
}

// Procesar búsqueda
$busqueda = '';
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $iglesia['nombre']; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenido a <?php echo $iglesia['nombre']; ?></h1>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="">
        <input type="hidden" name="iglesia_id" value="<?php echo $iglesia_id; ?>">
        <input type="text" name="busqueda" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- Formulario para agregar feligreses -->
    <form action="agregar_feligres.php" method="POST" class="form-dos-filas">
        <input type="hidden" name="iglesia_id" value="<?php echo $iglesia_id; ?>">
        
        <!-- Primera fila: Nombre -->
        <div class="fila">
            <input type="text" name="nombre" placeholder="Nombre" required>
        </div>

        <!-- Segunda fila: CI, Fecha de Nacimiento, Checkboxes y Botón -->
        <div class="fila">
            <input type="number" name="ci" placeholder="Cédula (opcional)">
            <input type="date" name="fecha_nacimiento" placeholder="Fecha de Nacimiento">
            <label>
                <input type="checkbox" name="bautizo" value="1"> Bautizado
            </label>
            <label>
                <input type="checkbox" name="confirmacion" value="1"> Confirmación
            </label>
            <label>
                <input type="checkbox" name="matrimonio" value="1"> Matrimonio
            </label>
            <input type="number" name="pag" placeholder="Página (opcional)">
            <button type="submit">Registrar</button>
        </div>
    </form>

    <!-- Enlace para cerrar sesión -->
    <a href="logout.php" class="btn-logout">Cerrar Sesión</a>

        <style>
        .btn-logout {
            display: inline-block;
            padding: 10px 20px;
            background-color:rgb(216, 168, 255); /* Rojo similar a Bootstrap */
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background-color:rgb(200, 159, 250); /* Un rojo más oscuro al pasar el mouse */
        }
        </style>


    <!-- Lista de feligreses -->
    </br></br>
    <h2>Lista de Feligreses</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>CI</th>
                <th>Fecha de Nacimiento</th>
                <th>Bautizo</th>
                <th>Confirmación</th>
                <th>Matrimonio</th>
                <th>Página</th>
                <th>Acciones</th>
            </tr>
        </thead>        
        <tbody>            
            <?php            
                        // Consulta para buscar feligreses
                        if (!empty($busqueda)) {
                            $stmt = $conn->prepare("
                                SELECT * FROM feligreses 
                                WHERE iglesia_id = :iglesia_id 
                                AND (nombre LIKE :busqueda OR ci LIKE :busqueda)
                            ");
                            $stmt->execute([
                                'iglesia_id' => $iglesia_id,
                                'busqueda' => "%$busqueda%"
                            ]);
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM feligreses WHERE iglesia_id = :iglesia_id");
                            $stmt->execute(['iglesia_id' => $iglesia_id]);
                        }
            
                        $feligreses = $stmt->fetchAll();
            
                        foreach ($feligreses as $feligres):
            ?>
                <tr>
                    <td><?php echo $feligres['nombre']; ?></td>
                    <td><?php echo $feligres['ci'] ?? 'N/A'; ?></td>
                    <td><?php echo $feligres['fecha_nacimiento'] ?? 'N/A'; ?></td>
                    <td>
                        <span class="icono <?php echo $feligres['bautizo'] ? 'bien' : 'mal'; ?>">
                            <?php echo $feligres['bautizo'] ? '✔' : '✘'; ?>
                        </span>
                    </td>
                    <td>
                        <span class="icono <?php echo $feligres['confirmacion'] ? 'bien' : 'mal'; ?>">
                            <?php echo $feligres['confirmacion'] ? '✔' : '✘'; ?>
                        </span>
                    </td>
                    <td>
                        <span class="icono <?php echo $feligres['matrimonio'] ? 'bien' : 'mal'; ?>">
                            <?php echo $feligres['matrimonio'] ? '✔' : '✘'; ?>
                        </span>
                    </td>
                    <td><?php echo $feligres['pag']; ?></td>
                    <td>
                    <a href="editar_feligres.php?id=<?php echo $feligres['id']; ?>" class="btn-editar">Editar</a>
                        <a href="eliminar_feligres.php?id=<?php echo $feligres['id']; ?>" class="btn-eliminar"
                           onclick="return confirmarEliminacion('<?php echo $feligres['nombre']; ?>')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Enlace para cerrar sesión -->
    <a href="logout.php" class="btn-logout">Cerrar Sesión</a>

        <style>
        .btn-logout {
            display: inline-block;
            padding: 10px 20px;
            background-color:rgb(216, 168, 255); /* Rojo similar a Bootstrap */
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background-color:rgb(200, 159, 250); /* Un rojo más oscuro al pasar el mouse */
        }
        </style>

    <script>
    function confirmarEliminacion(nombre) {
        return confirm(`¿Estás seguro de eliminar a "${nombre}"?`);
    }
    </script>
</body>
</html>