<?php
require_once("../../PHP/conexion.php");
$con = conectar_bd();

// Inicializar variables
$editar = false;
$nombre_editar = '';
$id_editar = '';

// Procesar acciones del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    if ($accion === 'crear' && !empty($_POST['nombre'])) {
        $nombre = trim($_POST['nombre']);
        $stmt = mysqli_prepare($con, "INSERT INTO asignatura (nombre) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $nombre);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Adscripta/HTML/asignacionAsig.php");
        exit;
    } elseif ($accion === 'editar' && !empty($_POST['id_asignatura']) && !empty($_POST['nombre'])) {
        $id = intval($_POST['id_asignatura']);
        $nombre = trim($_POST['nombre']);
        $stmt = mysqli_prepare($con, "UPDATE asignatura SET nombre = ? WHERE id_asignatura = ?");
        mysqli_stmt_bind_param($stmt, "si", $nombre, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Adscripta/HTML/asignacionAsig.php");
        exit;
    } elseif ($accion === 'eliminar' && !empty($_POST['id_asignatura'])) {
        $id = intval($_POST['id_asignatura']);
        $stmt = mysqli_prepare($con, "DELETE FROM asignatura WHERE id_asignatura = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../../Adscripta/HTML/asignacionAsig.php");
        exit;
    }
}

// Si se va a editar, cargar los datos
if (isset($_GET['editar'])) {
    $editar = true;
    $id_editar = intval($_GET['editar']);
    $res = mysqli_query($con, "SELECT * FROM asignatura WHERE id_asignatura = $id_editar LIMIT 1");
    if ($fila = mysqli_fetch_assoc($res)) {
        $nombre_editar = $fila['nombre'];
    }
}

// Cargar todas las asignaturas
$asignaturas = [];
$res = mysqli_query($con, "SELECT * FROM asignatura ORDER BY nombre ASC");
while ($fila = mysqli_fetch_assoc($res)) {
    $asignaturas[] = $fila;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de asignaturas</title>
    <link rel="stylesheet" href="../../Css/style.css">
    <!-- Material Icons CDN -->
     <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bodyregidat">
<?php include '../../HEADERS/headerA.php'; ?>
    <main class="sugarads-main">
        <h1 class="sugarads-title">Gestión de asignaturas</h1>
        <div class="sugarads-grid registro-datos">
            <section class="sugarads-col-left">
                <!-- Formulario para crear o editar asignaturas -->
                <form class="formasig" method="post" action="../../Adscripta/PHP/asignaturaFunciones.php">
                    <h2 class="h2asiges"><?= $editar ? "Editar asignatura" : "Nueva asignatura" ?></h2>
                    <div class="sugarads-field">
                        <input type="text" id="nombre" name="nombre" class="inputasig" required placeholder="Ejemplo: Matemática" value="<?= htmlspecialchars($nombre_editar) ?>">
                    </div>
                    <?php if ($editar): ?>
                        <input type="hidden" name="id_asignatura" value="<?= $id_editar ?>">
                        <input type="hidden" name="accion" value="editar">
                        <div class="sugarads-field">
                            <button type="submit" class="sugarads-btn sugarads-btn-guardar">Guardar Cambios</button>
                            <a href="asignacionAsig.php" class="sugarads-btn sugarads-btn-cancelar">Cancelar</a>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="accion" value="crear">
                        <div class="sugarads-field">
                            <button type="submit" class="regasigboton">Registrar</button>
                            <button type="reset" class="botoneliminar">Cancelar</button>
                        </div>
                    <?php endif; ?>
                </form>
                <br>
                <!-- Listado y acciones -->
                <div class="sugarads-entradas sombra">
                    <h2 class="h2asiges1">Asignaturas registradas</h2>
                    <?php if ($asignaturas): ?>
                        <ul>
                       <?php foreach ($asignaturas as $a): ?>
                            <li class="pruebads">
                                <?= htmlspecialchars($a['nombre']) ?>
                                <a href="../../Adscripta/HTML/asignacionAsig.php?editar=<?= $a['id_asignatura'] ?>" class="sugarads-btn sugarads-btn-editar">Editar</a>
                                <form method="post" action="" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta asignatura?');">
                                    <input type="hidden" name="id_asignatura" value="<?= $a['id_asignatura'] ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="botoneliminar">Eliminar</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay asignaturas registradas.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
