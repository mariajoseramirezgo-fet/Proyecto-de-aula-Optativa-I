<?php
session_start();
include("conexion.php");

/* VALIDAR SESIÓN */
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/* =========================
    GUARDAR / EDITAR PRODUCTO
========================= */

if (isset($_POST['guardar'])) {

    $id = $_POST['id_producto'] ?? "";

    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $categoria = mysqli_real_escape_string($conexion, $_POST['categoria']);
    $precio = mysqli_real_escape_string($conexion, $_POST['precio']);
    $precio_alquiler = mysqli_real_escape_string($conexion, $_POST['precio_alquiler']);
    $stock = mysqli_real_escape_string($conexion, $_POST['stock']);

    $imagenSQL = "";
    $nombreImagen = "";

    /* IMAGEN */

    if (
        isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] == 0
    ) {

        $carpeta = "img/productos/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreImagen =
        time() . "_" .
        basename($_FILES['imagen']['name']);

        $ruta = $carpeta . $nombreImagen;

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $ruta
        );

        $imagenSQL = ", imagen='$nombreImagen'";
    }

    /* =========================
        EDITAR PRODUCTO
    ========================= */

    if ($id != "") {

        $sqlEditar = "UPDATE producto SET

            nombre='$nombre',
            id_categoria='$categoria',
            precio_venta='$precio',
            precio_alquiler='$precio_alquiler',
            stock_total='$stock',
            stock_disponible='$stock'
            $imagenSQL

        WHERE id_producto='$id'";

        mysqli_query($conexion, $sqlEditar);

        /* ACTUALIZAR INVENTARIO */

        $sqlInventario = "UPDATE inventario SET

            stock_actual='$stock'

        WHERE id_producto='$id'";

        mysqli_query($conexion, $sqlInventario);
    }

    /* =========================
       CREAR PRODUCTO
    ========================= */

    else {

        $sqlCrear = "INSERT INTO producto
        (
            nombre,
            id_categoria,
            precio_venta,
            precio_alquiler,
            stock_total,
            stock_disponible,
            imagen
        )

        VALUES

        (
            '$nombre',
            '$categoria',
            '$precio',
            '$precio_alquiler',
            '$stock',
            '$stock',
            '$nombreImagen'
        )";

        mysqli_query($conexion, $sqlCrear);

        $id_producto = mysqli_insert_id($conexion);

        /* INVENTARIO */

        $sqlInventario = "INSERT INTO inventario
        (
            id_producto,
            stock_actual,
            stock_minimo,
            ubicacion
        )

        VALUES

        (
            '$id_producto',
            '$stock',
            1,
            'Bodega'
        )";

        mysqli_query($conexion, $sqlInventario);
    }

    header("Location: inventario.php");
    exit();
}

/* =========================
   CONSULTA PRODUCTOS
========================= */

$sql = "SELECT p.*, i.stock_actual

FROM producto p

INNER JOIN inventario i
ON p.id_producto = i.id_producto

ORDER BY p.id_producto DESC";

$resultado = mysqli_query($conexion, $sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inventario - SportHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/inventario.css?v=2">
    <link rel="stylesheet" href="css/mediaqueryspagina.css?v=1">
    <script src="https://kit.fontawesome.com/9d1a86738f.js" crossorigin="anonymous"></script>

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

    <a href="logout.php">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
    </a>

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

        <h2>GESTIÓN DE INVENTARIO</h2>

        <button
        class="btn-nuevo"
        onclick="abrirModal()"
        >
            + NUEVO PRODUCTO
        </button>

        <div class="tabla-productos">

            <div class="tabla-header">

                <span>Imagen</span>
                <span>Producto</span>
                <span>Categoría</span>
                <span>Precio Venta</span>
                <span>Precio Alquiler</span>
                <span>Stock</span>
                <span>Acciones</span>

            </div>

            <?php while($row = mysqli_fetch_assoc($resultado)) { ?>

            <div class="producto">

                <div>

                    <img
                    src="img/productos/<?php echo $row['imagen']; ?>"
                    width="60"
                    height="60"
                    style="object-fit:cover;border-radius:10px;"
                    >

                </div>

                <div class="producto-info">

                    <div>

                        <b>
                            <?php echo $row['nombre']; ?>
                        </b>

                        <br>

                        <small>
                            SKU:
                            <?php echo $row['id_producto']; ?>
                        </small>

                    </div>

                </div>

                <span>
                    <?php echo $row['id_categoria']; ?>
                </span>

                <span>
                    $<?php echo number_format($row['precio_venta']); ?>
                </span>

                <span>
                    $<?php echo number_format($row['precio_alquiler']); ?>
                </span>

                <span class="stock">
                    <?php echo $row['stock_actual']; ?>
                </span>

                <div class="acciones-producto">

                    <i
                    class="fa-solid fa-pen-to-square editar"

                    onclick="editarProducto(
                    '<?php echo $row['id_producto']; ?>',
                    '<?php echo htmlspecialchars($row['nombre'], ENT_QUOTES); ?>',
                    '<?php echo $row['id_categoria']; ?>',
                    '<?php echo $row['precio_venta']; ?>',
                    '<?php echo $row['precio_alquiler']; ?>',
                    '<?php echo $row['stock_actual']; ?>'
                    )">
                    </i>

                    <i
                    class="fa-solid fa-trash eliminar"

                    onclick="eliminarProducto(
                    <?php echo $row['id_producto']; ?>
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

        <h3>Nuevo Producto</h3>

        <form
        method="POST"
        enctype="multipart/form-data"
        >

            <!-- ID OCULTO -->

            <input
            type="hidden"
            name="id_producto"
            id="id_producto"
            >

            <input
            name="nombre"
            type="text"
            placeholder="Nombre"
            required
            >

            <select name="categoria" required>

                <option value="">
                    Selecciona una categoría
                </option>

                <?php

                $sqlCategorias = "SELECT * FROM categoria";

                $resCategorias = mysqli_query(
                    $conexion,
                    $sqlCategorias
                );

                while($cat = mysqli_fetch_assoc($resCategorias)){

                ?>

                <option
                value="<?php echo $cat['id_categoria']; ?>"
                >

                    <?php echo $cat['nombre']; ?>

                </option>

                <?php } ?>

            </select>

            <input
            name="precio"
            type="number"
            placeholder="Precio venta"
            required
            >

            <input
            name="precio_alquiler"
            type="number"
            placeholder="Precio alquiler"
            required
            >

            <input
            name="stock"
            type="number"
            placeholder="Stock"
            required
            >

            <input
            type="file"
            name="imagen"
            accept="image/*"
            >

            <button type="submit" name="guardar">
                Guardar
            </button>

            <button
            type="button"
            onclick="cerrarModal()"
            >
                Cancelar
            </button>

        </form>

    </div>

</div>

<script>

/* SIDEBAR */

const btn = document.getElementById("menu-toggle");
const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", ()=>{

    sidebar.classList.toggle("active");

});

/* LOGOUT */

function logout(){

    localStorage.clear();

    window.location.href = "index.php";

}

/* MODAL */

function abrirModal(){

    document.getElementById(
    "modal"
    ).style.display = "block";

    /* LIMPIAR */

    document.getElementById(
    "id_producto"
    ).value = "";

    document.querySelector(
    'input[name="nombre"]'
    ).value = "";

    document.querySelector(
    'select[name="categoria"]'
    ).value = "";

    document.querySelector(
    'input[name="precio"]'
    ).value = "";

    document.querySelector(
    'input[name="precio_alquiler"]'
    ).value = "";

    document.querySelector(
    'input[name="stock"]'
    ).value = "";
}

function cerrarModal(){

    document.getElementById(
    "modal"
    ).style.display = "none";

}

/* ELIMINAR */

function eliminarProducto(id){

    if(confirm(
    "¿Deseas eliminar este producto?"
    )){

        window.location.href =
        "eliminar_producto.php?id=" + id;
    }
}

/* EDITAR */

function editarProducto(
id,
nombre,
categoria,
precio,
alquiler,
stock
){

    abrirModal();

    document.getElementById(
    "id_producto"
    ).value = id;

    document.querySelector(
    'input[name="nombre"]'
    ).value = nombre;

    document.querySelector(
    'select[name="categoria"]'
    ).value = categoria;

    document.querySelector(
    'input[name="precio"]'
    ).value = precio;

    document.querySelector(
    'input[name="precio_alquiler"]'
    ).value = alquiler;

    document.querySelector(
    'input[name="stock"]'
    ).value = stock;
}

</script>

</body>
</html>