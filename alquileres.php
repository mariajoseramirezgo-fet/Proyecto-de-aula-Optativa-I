<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN

if (!isset($_SESSION['usuario'])) {

    header("Location: index.php");
    exit();
}

/* =========================
    GUARDAR / EDITAR ALQUILER
========================= */

if(isset($_POST['alquilar'])){

    $id_alquiler = $_POST['id_alquiler'] ?? "";

    // LIMPIAR DATOS

    $producto = mysqli_real_escape_string(
        $conexion,
        $_POST['producto']
    );

    $cliente = mysqli_real_escape_string(
        $conexion,
        $_POST['cliente']
    );

    $total = mysqli_real_escape_string(
        $conexion,
        $_POST['precio']
    );

    $cantidad = mysqli_real_escape_string(
        $conexion,
        $_POST['cantidad']
    );

    $estado = mysqli_real_escape_string(
        $conexion,
        $_POST['estado']
    );

    // BUSCAR CLIENTE

    $buscar_cliente = mysqli_query($conexion, "

    SELECT id_cliente

    FROM cliente

    WHERE nombre = '$cliente'

    ");

    if(mysqli_num_rows($buscar_cliente) == 0){

        echo "
        <script>

            alert('Cliente no encontrado');

            window.location='alquileres.php';

        </script>
        ";

        exit();
    }

    $datos_cliente = mysqli_fetch_assoc($buscar_cliente);

    $id_cliente = $datos_cliente['id_cliente'];

    // EMPLEADO

    $id_empleado =
    $_SESSION['usuario']['id_usuario'] ?? 1;

    /* =========================
        EDITAR
    ========================= */

    if($id_alquiler != ""){

        $sqlEditar = "UPDATE alquiler SET

        producto='$producto',
        id_cliente='$id_cliente',
        estado='$estado',
        total='$total'

        WHERE id_alquiler='$id_alquiler'";

        mysqli_query($conexion, $sqlEditar);
    }

    /* =========================
        CREAR
    ========================= */

    else{

        $sql = "INSERT INTO alquiler
        (
            producto,
            id_cliente,
            id_empleado,
            fecha_inicio,
            fecha_fin,
            estado,
            total
        )

        VALUES

        (
            '$producto',
            '$id_cliente',
            '$id_empleado',
            NOW(),
            DATE_ADD(NOW(), INTERVAL 5 DAY),
            '$estado',
            '$total'
        )";

        mysqli_query($conexion, $sql);
    }

    header("Location: alquileres.php");
    exit();
}

/* =========================
    ELIMINAR
========================= */

if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    mysqli_query($conexion, "

    DELETE FROM alquiler

    WHERE id_alquiler='$id'

    ");

    header("Location: alquileres.php");
    exit();
}

/* =========================
    LISTAR ALQUILERES
========================= */

$alquileres = mysqli_query($conexion, "

SELECT
a.*,
c.nombre AS cliente_nombre

FROM alquiler a

INNER JOIN cliente c
ON a.id_cliente = c.id_cliente

ORDER BY a.id_alquiler DESC

");

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Alquileres - SportHub</title>

    <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
    >

    <link rel="stylesheet" href="css/reset.css">

    <link
    rel="stylesheet"
    href="css/alquileres.css?v=1"
    >

    <link
    rel="stylesheet"
    href="css/mediaqueryspagina.css?v=1"
    >

    <script
    src="https://kit.fontawesome.com/9d1a86738f.js">
    </script>

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

    <!-- MAIN -->

    <main class="main-content">

        <h2>GESTIÓN DE ALQUILERES</h2>

        <p>
            Alquila equipamiento y accesorios deportivos a tus clientes.
        </p>

        <div class="alquileres-container">

            <!-- FORM -->

            <div class="card-form">

                <h3>NUEVO ALQUILER</h3>

                <form method="POST">

                    <input
                    type="hidden"
                    name="id_alquiler"
                    id="id_alquiler"
                    >

                    <label>Producto</label>

                    <input
                    type="text"
                    name="producto"
                    id="producto"
                    required
                    >

                    <label>Cliente</label>

                    <input
                    type="text"
                    name="cliente"
                    id="cliente"
                    required
                    >

                    <div class="row">

                        <div>

                            <label>Precio</label>

                            <input
                            type="number"
                            name="precio"
                            id="precio"
                            required
                            >

                        </div>

                        <div>

                            <label>Cantidad</label>

                            <input
                            type="number"
                            name="cantidad"
                            id="cantidad"
                            value="1"
                            >

                        </div>

                    </div>

                    <label>Estado</label>

                    <select name="estado" id="estado">

                        <option value="Activo">
                            Activo
                        </option>

                        <option value="Pendiente">
                            Pendiente
                        </option>

                        <option value="Devuelto">
                            Devuelto
                        </option>

                    </select>

                    <button
                    type="submit"
                    name="alquilar"
                    class="btn-alquilar"
                    >
                        GUARDAR ALQUILER
                    </button>

                </form>

            </div>

            <!-- TABLA -->

            <div class="card-tabla">

                <h3>LISTA DE ALQUILERES</h3>

                <div class="tabla-header">

                    <span>ID</span>
                    <span>PRODUCTO</span>
                    <span>CLIENTE</span>
                    <span>ESTADO</span>
                    <span>INICIO</span>
                    <span>DEVOLUCIÓN</span>
                    <span>ACCIONES</span>

                </div>

                <?php while($a = mysqli_fetch_assoc($alquileres)) { ?>

                <div class="fila">

                    <span>
                        #<?php echo $a['id_alquiler']; ?>
                    </span>

                    <span>
                        <?php echo $a['producto']; ?>
                    </span>

                    <div class="cliente">

                        <i class="fa-regular fa-user"></i>

                        <?php echo $a['cliente_nombre']; ?>

                    </div>

                    <span>
                        <?php echo $a['estado']; ?>
                    </span>

                    <span>
                        <?php echo $a['fecha_inicio']; ?>
                    </span>

                    <div class="fecha">

                        <i class="fa-regular fa-calendar"></i>

                        <?php echo $a['fecha_fin']; ?>

                    </div>

                    <div class="acciones">

                        <!-- EDITAR -->

                        <i
                        class="fa-solid fa-pen-to-square editar"

                        onclick="editarAlquiler(
                        '<?php echo $a['id_alquiler']; ?>',
                        '<?php echo htmlspecialchars($a['producto'], ENT_QUOTES); ?>',
                        '<?php echo htmlspecialchars($a['cliente_nombre'], ENT_QUOTES); ?>',
                        '<?php echo $a['total']; ?>',
                        '<?php echo $a['estado']; ?>'
                        )">
                        </i>

                        <!-- ELIMINAR -->

                        <i
                        class="fa-solid fa-trash eliminar"

                        onclick="eliminarAlquiler(
                        <?php echo $a['id_alquiler']; ?>
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

function logout(){

    window.location.href = "logout.php";

}

const btn = document.getElementById("menu-toggle");

const sidebar = document.getElementById("sidebar");

btn.addEventListener("click",()=>{

    sidebar.classList.toggle("active");

});

/* =========================
    ELIMINAR
========================= */

function eliminarAlquiler(id){

    if(confirm("¿Eliminar alquiler?")){

        window.location.href =
        "alquileres.php?eliminar=" + id;
    }
}

/* =========================
    EDITAR
========================= */

function editarAlquiler(
id,
producto,
cliente,
precio,
estado
){

    document.getElementById(
    "id_alquiler"
    ).value = id;

    document.getElementById(
    "producto"
    ).value = producto;

    document.getElementById(
    "cliente"
    ).value = cliente;

    document.getElementById(
    "precio"
    ).value = precio;

    document.getElementById(
    "estado"
    ).value = estado;
}

</script>

</body>
</html>