<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN

if (!isset($_SESSION['usuario'])) {

    header("Location: index.php");
    exit();
}

/* =========================
   GUARDAR TICKET
========================= */

if(isset($_POST['guardar'])){

    $nombreProducto = mysqli_real_escape_string(
        $conexion,
        $_POST['producto']
    );

    $problema = $_POST['problema'];

    $detalle = $_POST['detalle'];

    $reportado = $_POST['reportado'];

    $prioridad = $_POST['prioridad'];

    /* =========================
       BUSCAR PRODUCTO
    ========================= */

    $buscarProducto = mysqli_query($conexion, "

    SELECT id_producto
    FROM producto
    WHERE nombre='$nombreProducto'

    ");

    if(mysqli_num_rows($buscarProducto) == 0){

        die("Producto no encontrado");
    }

    $productoData = mysqli_fetch_assoc($buscarProducto);

    $id_producto = $productoData['id_producto'];

    /* =========================
       INSERTAR
    ========================= */

    $sql = "INSERT INTO ticket
    (
        id_producto,
        id_empleado,
        tipo,
        descripcion,
        estado,
        fecha,
        prioridad
    )

    VALUES

    (
        '$id_producto',
        1,
        '$problema',
        '$detalle',
        'ABIERTO',
        NOW(),
        '$prioridad'
    )";

    if(!mysqli_query($conexion, $sql)){

        die("Error: " . mysqli_error($conexion));
    }

    header("Location: tickets.php");
    exit();
}

/* =========================
   ELIMINAR
========================= */

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    mysqli_query(
        $conexion,
        "DELETE FROM ticket WHERE id_ticket=$id"
    );

    header("Location: tickets.php");

    exit();
}

/* =========================
   EDITAR
========================= */

if(isset($_POST['editar'])){

    $id_ticket = $_POST['id_ticket'];

    $problema = $_POST['problema'];

    $detalle = $_POST['detalle'];

    $prioridad = $_POST['prioridad'];

    $estado = $_POST['estado'];

    $sqlEditar = "UPDATE ticket SET

    tipo = '$problema',
    descripcion = '$detalle',
    prioridad = '$prioridad',
    estado = '$estado'

    WHERE id_ticket = '$id_ticket'";

    mysqli_query($conexion, $sqlEditar);

    header("Location: tickets.php");

    exit();
}

/* =========================
   CONSULTA
========================= */

$sql = "

SELECT
t.*,
p.nombre AS producto

FROM ticket t

INNER JOIN producto p
ON t.id_producto = p.id_producto

";

$resultado = mysqli_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Tickets - SportHub</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet"href="css/tickets.css?=2">
<link rel="stylesheet"href="css/mediaquerystickets.css?=1">

<script src="https://kit.fontawesome.com/9d1a86738f.js"></script>

</head>

<body>

<header class="header">

    <div class="menu">

        <button id="menu-toggle">

            <i class="fa-solid fa-bars"></i>

        </button>

        <div class="logo">

            <img src="img/logo_dashboard.png">

        </div>

    </div>

    <div class="search">

        <input
        type="text"
        placeholder="Buscar..."
        >

        <i class="fa-solid fa-magnifying-glass"></i>

    </div>

    <div class="icons">

        <button onclick="logout()">

            <i class="fa-solid fa-arrow-right-from-bracket"></i>

        </button>

    </div>

    <div class="user">

        <div>

            <p>
                <b>
                    <?php echo $_SESSION['usuario']['nombre']; ?>
                </b>
            </p>

            <small>Administrador</small>

        </div>

        <img src="img/avatar.png">

    </div>

</header>

<div class="container">

    <!-- SIDEBAR -->

    <aside id="sidebar" class="sidebar">

        <ul>

            <li>

                <a href="dashboard.php">

                    <i class="fa-solid fa-house"></i>

                    <span>Dashboard</span>

                </a>

            </li>

            <li>

                <a href="inventario.php">

                    <i class="fa-solid fa-box"></i>

                    <span>Inventario</span>

                </a>

            </li>

            <li>

                <a href="alquileres.php">

                    <i class="fa-solid fa-calendar"></i>

                    <span>Alquileres</span>

                </a>

            </li>

            <li>

                <a href="reportes.php">

                    <i class="fa-solid fa-chart-line"></i>

                    <span>Reportes</span>

                </a>

            </li>

            <li>

                <a href="tickets.php">

                    <i class="fa-solid fa-ticket"></i>

                    <span>Tickets</span>

                </a>

            </li>

            <li>

                <a href="clientes.php">

                    <i class="fa-solid fa-users"></i>

                    <span>Clientes</span>

                </a>

            </li>

            <li>

                <a href="configuracion.php">

                    <i class="fa-solid fa-gear"></i>

                    <span>Configuración</span>

                </a>

            </li>

        </ul>

    </aside>

    <!-- CONTENIDO -->

    <main class="main-content">

        <h2>Sistema de tickets</h2>

        <p>
            Controla problemas, reportes y estado de productos.
        </p>

        <button
        class="btn-nuevo"
        onclick="abrirModal()"
        >
            + NUEVO TICKET
        </button>

        <div class="tabla-productos">

            <div class="tabla-header">

                <span>Producto</span>

                <span>Problema</span>

                <span>Descripción</span>

                <span>Prioridad</span>

                <span>Estado</span>

                <span>Acciones</span>

            </div>

            <?php while($row = mysqli_fetch_assoc($resultado)) { ?>

            <div class="producto">

                <div class="producto-info">

                    <i class="fa-solid fa-box"></i>

                    <div>

                        <b>
                            <?php echo $row['producto']; ?>
                        </b>

                    </div>

                </div>

                <div class="problema">

                    <?php echo $row['tipo']; ?>

                </div>

                <div>

                    <?php echo $row['descripcion']; ?>

                </div>

                <span class="prioridad <?php echo strtolower($row['prioridad']); ?>">

                    <?php echo $row['prioridad']; ?>

                </span>

                <span class="estado abierto">

                    <?php echo $row['estado']; ?>

                </span>

                <div class="acciones">

                    <i
                    class="fa-solid fa-trash"

                    onclick="confirmarEliminar(
                    <?php echo $row['id_ticket']; ?>
                    )">
                    </i>

                    <i
                    class="fa-solid fa-pencil"

                    onclick="abrirModalEditar(

                    '<?php echo $row['id_ticket']; ?>',

                    '<?php echo $row['producto']; ?>',

                    '<?php echo $row['tipo']; ?>',

                    '<?php echo $row['descripcion']; ?>',

                    '<?php echo $row['prioridad']; ?>',

                    '<?php echo $row['estado']; ?>'

                    )">
                    </i>

                </div>

            </div>

            <?php } ?>

        </div>

    </main>

</div>

<!-- MODAL -->

<div id="modal" class="modal">

    <div class="modal-content">

        <h3>Ticket</h3>

        <form method="POST">

            <input
            type="hidden"
            name="id_ticket"
            id="id_ticket"
            >

            <input
            name="producto"
            id="producto"
            type="text"
            placeholder="Producto"
            required
            >

            <input
            name="problema"
            id="problema"
            type="text"
            placeholder="Problema"
            required
            >

            <input
            name="detalle"
            id="detalle"
            type="text"
            placeholder="Detalle"
            required
            >

            <input
            name="reportado"
            id="reportado"
            type="text"
            placeholder="Reportado por"
            required
            >

            <select name="prioridad" id="prioridad">

                    <option value="BAJA">
                        Baja
                    </option>

                    <option value="MEDIA">
                        Media
                    </option>

                    <option value="ALTA">
                        Alta
                    </option>

                </select>

            <select name="estado" id="estado">

                    <option value="ABIERTO">
                        ABIERTO
                    </option>

                    <option value="EN PROCESO">
                        EN PROCESO
                    </option>

                    <option value="CERRADO">
                        CERRADO
                    </option>

                </select>

                <div class="modal-buttons">

    <button
    type="submit"
    name="guardar"
    id="btnGuardar">
        Guardar
    </button>

    <button
    type="submit"
    name="editar"
    id="btnEditar"
    style="display:none;">
        Editar
    </button>

    <button
    type="button"
    onclick="cerrarModal()">
        Cancelar
    </button>

</div>

            </div>

        </form>

    </div>

</div>

<footer class="footer">

    <small>Mariajose © 2026</small>

</footer>

<script>

// LOGOUT

function logout(){

    window.location.href = "logout.php";

}

// SIDEBAR

document.getElementById("menu-toggle")
.addEventListener("click", () => {

    document.getElementById("sidebar")
    .classList.toggle("active");

});

// MODAL

function abrirModal(){

    document.getElementById("modal").style.display = "block";

    // LIMPIAR

    document.getElementById("id_ticket").value = "";

    document.getElementById("producto").value = "";

    document.getElementById("problema").value = "";

    document.getElementById("detalle").value = "";

    document.getElementById("reportado").value = "";

    document.getElementById("prioridad").value = "MEDIA";

    document.getElementById("estado").value = "ABIERTO";

    // BOTONES

    document.getElementById("btnGuardar").style.display =
    "inline-block";

    document.getElementById("btnEditar").style.display =
    "none";

}

function cerrarModal(){

    document.getElementById("modal").style.display = "none";

}

// EDITAR

function abrirModalEditar(
    id,
    producto,
    problema,
    detalle,
    prioridad,
    estado
){

    document.getElementById("modal").style.display = "block";

    // LLENAR CAMPOS

    document.getElementById("id_ticket").value = id;

    document.getElementById("producto").value = producto;

    document.getElementById("problema").value = problema;

    document.getElementById("detalle").value = detalle;

    document.getElementById("prioridad").value = prioridad;

    document.getElementById("estado").value = estado;

    // BOTONES

    document.getElementById("btnGuardar").style.display =
    "none";

    document.getElementById("btnEditar").style.display =
    "inline-block";

}

// CONFIRMAR ELIMINAR

function confirmarEliminar(id){

    if(confirm(
    "¿Seguro que deseas eliminar este ticket?"
    )){

        window.location.href =
        "tickets.php?eliminar=" + id;

    }

}

</script>

</body>
</html>