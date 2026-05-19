<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN

if (!isset($_SESSION['usuario'])) {

    header("Location: index.php");
    exit();
}

/* =========================
   GUARDAR CLIENTE
========================= */

if(isset($_POST['guardar'])){

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre']
    );

    $apellido = mysqli_real_escape_string(
        $conexion,
        $_POST['apellido']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo']
    );

    $telefono = mysqli_real_escape_string(
        $conexion,
        $_POST['telefono']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password']
    );

    $rol = "cliente";

    $sql = "INSERT INTO cliente
    (
        nombre,
        apellido,
        correo,
        telefono,
        password,
        rol
    )

    VALUES

    (
        '$nombre',
        '$apellido',
        '$correo',
        '$telefono',
        '$password',
        '$rol'
    )";

    mysqli_query($conexion, $sql);

    header("Location: clientes.php");
    exit();
}

/* =========================
   ELIMINAR
========================= */

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    mysqli_query(
        $conexion,
        "DELETE FROM cliente WHERE id_cliente='$id'"
    );

    header("Location: clientes.php");
    exit();
}

/* =========================
   EDITAR
========================= */

if(isset($_POST['editar'])){

    $id = $_POST['id'];

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre']
    );

    $apellido = mysqli_real_escape_string(
        $conexion,
        $_POST['apellido']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo']
    );

    $telefono = mysqli_real_escape_string(
        $conexion,
        $_POST['telefono']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password']
    );

    $sql = "UPDATE cliente SET

    nombre='$nombre',
    apellido='$apellido',
    correo='$correo',
    telefono='$telefono',
    password='$password'

    WHERE id_cliente='$id'";

    mysqli_query($conexion, $sql);

    header("Location: clientes.php");
    exit();
}

/* =========================
    LISTAR CLIENTES
========================= */

$clientes = mysqli_query(
    $conexion,
    "SELECT * FROM cliente ORDER BY id_cliente DESC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Clientes - TICKETS-FET</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/clientes.css?v=2">
<link rel="stylesheet" href="css/mediaquerystickets.css">
<script src="https://kit.fontawesome.com/9d1a86738f.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <a href="logout.php">

            <i class="fa-solid fa-arrow-right-from-bracket"></i>

        </a>

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

    <!-- MAIN -->

    <main class="main-content">

        <h2>Directorio de Clientes</h2>

        <p>
            Administra la información de tus clientes.
        </p>

        <div class="alquileres-container">

            <!-- FORM -->

            <div class="card-form">

                <h3 id="tituloForm">

                    NUEVO CLIENTE

                </h3>

                <form method="POST">

                    <input
                    type="hidden"
                    name="id"
                    id="id"
                    >

                    <label>Nombre</label>

                    <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    required
                    >

                    <label>Apellido</label>

                    <input
                    type="text"
                    name="apellido"
                    id="apellido"
                    required
                    >

                    <label>Correo</label>

                    <input
                    type="email"
                    name="correo"
                    id="correo"
                    required
                    >

                    <label>Teléfono</label>

                    <input
                    type="text"
                    name="telefono"
                    id="telefono"
                    required
                    >

                    <div class="botones-form">

                        <button
                        type="submit"
                        name="guardar"
                        id="btnGuardar"
                        class="btn-alquilar"
                        >

                            Guardar

                        </button>

                        <button
                        type="submit"
                        name="editar"
                        id="btnEditar"
                        class="btn-editar"
                        style="display:none;"
                        >

                            Actualizar

                        </button>

                    </div>

                </form>

            </div>

            <!-- TABLA -->

            <div class="card-tabla">

                <h3>LISTA DE CLIENTES</h3>

                <div class="tabla-header">

                    <span>ID</span>
                    <span>NOMBRE</span>
                    <span>APELLIDO</span>
                    <span>CORREO</span>
                    <span>TELÉFONO</span>
                    <span>ROL</span>
                    <span>ACCIONES</span>

                </div>

                <?php while($c = mysqli_fetch_assoc($clientes)){ ?>

                <div class="fila">

                    <span>

                        <?php echo $c['id_cliente']; ?>

                    </span>

                    <span>

                        <?php echo $c['nombre']; ?>

                    </span>

                    <span>

                        <?php echo $c['apellido']; ?>

                    </span>

                    <span>

                        <?php echo $c['correo']; ?>

                    </span>

                    <span>

                        <?php echo $c['telefono']; ?>

                    </span>

                    <span>

                        <?php echo $c['rol']; ?>

                    </span>

                    <div class="acciones">

                        <!-- ELIMINAR -->

                        <i
                        class="fa-solid fa-trash"

                        onclick="eliminarCliente(
                        <?php echo $c['id_cliente']; ?>
                        )">
                        </i>

                        <!-- EDITAR -->

                        <i
                        class="fa-solid fa-pen-to-square"

                        onclick="editarCliente(

                        '<?php echo $c['id_cliente']; ?>',

                        '<?php echo $c['nombre']; ?>',

                        '<?php echo $c['apellido']; ?>',

                        '<?php echo $c['correo']; ?>',

                        '<?php echo $c['telefono']; ?>',

                        )">
                        </i>

                    </div>

                </div>

                <?php } ?>

            </div>

        </div>

    </main>

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
.addEventListener("click",()=>{

    document.getElementById("sidebar")
    .classList.toggle("active");

});

// ELIMINAR

function eliminarCliente(id){

    if(confirm(
    "¿Seguro que quieres eliminar este cliente?"
    )){

        window.location =
        "clientes.php?eliminar=" + id;

    }

}

// EDITAR

function editarCliente(
    id,
    nombre,
    apellido,
    correo,
    telefono,
    password
){

    // TITULO

    document.getElementById(
    "tituloForm"
    ).innerHTML = "EDITAR CLIENTE";

    // INPUTS

    document.getElementById(
    "id"
    ).value = id;

    document.getElementById(
    "nombre"
    ).value = nombre;

    document.getElementById(
    "apellido"
    ).value = apellido;

    document.getElementById(
    "correo"
    ).value = correo;

    document.getElementById(
    "telefono"
    ).value = telefono;

    // BOTONES

    document.getElementById(
    "btnGuardar"
    ).style.display = "none";

    document.getElementById(
    "btnEditar"
    ).style.display = "block";

}

</script>

</body>
</html>