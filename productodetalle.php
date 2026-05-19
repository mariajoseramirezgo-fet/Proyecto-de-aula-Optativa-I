<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// VALIDAR ROL USER
if ($_SESSION['usuario']['rol'] != "user") {
    header("Location: producto.php");
    exit();
}

/* =========================
   OBTENER ID PRODUCTO
========================= */
$id = $_GET['id'] ?? 0;

/* =========================
   CONSULTA PRODUCTO REAL
========================= */
$sql = "SELECT p.*, i.stock_actual
FROM producto p
INNER JOIN inventario i ON p.id_producto = i.id_producto
WHERE p.id_producto = $id";

$resultado = mysqli_query($conexion, $sql);

if(mysqli_num_rows($resultado) == 0){
    echo "Producto no encontrado";
    exit();
}

$producto = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Producto Detalle</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/productodetalle.css">

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

<div class="user">

<p>
<b><?php echo $_SESSION['usuario']['nombre']; ?></b>
</p>

<small>Usuario</small>

</div>

</header>

<div class="container">

<!-- SIDEBAR -->

<aside id="sidebar" class="sidebar">

<ul>

<li>
<a href="userinicio.php">
<i class="fa-solid fa-house"></i>
<span>Inicio</span>
</a>
</li>

<li>
<a href="producto.php">
<i class="fa-solid fa-box"></i>
<span>Productos</span>
</a>
</li>

<li>
<a href="alquileruser.php">
<i class="fa-solid fa-calendar"></i>
<span>Alquiler</span>
</a>
</li>

<li>
<a href="configuracionuser.php">
<i class="fa-solid fa-gear"></i>
<span>Configuración</span>
</a>
</li>

</ul>

</aside>

<!-- MAIN -->

<main class="main-content">

<div class="detalle-producto">

<!-- IMAGEN -->

<div class="img-container">

<img
src="img/productos/<?php echo $producto['imagen']; ?>"
width="300"
>

</div>

<!-- INFO -->

<div class="info-producto">

<h2>
<?php echo $producto['nombre']; ?>
</h2>

<p class="precio">

$<?php echo number_format($producto['precio_venta']); ?>

</p>

<p>

<?php
echo $producto['descripcion']
?? 'Producto disponible en tienda';
?>

</p>

<p>

<b>Stock:</b>

<?php echo $producto['stock_actual']; ?>

</p>

<div class="acciones">

<button
class="btn comprar"
onclick="abrirModal()">

Comprar

</button>

<button
class="btn carrito"
onclick="carrito()">

Carrito

</button>

<button
class="btn alquilar"
onclick="alquilar()">

Alquilar

</button>

</div>

</div>

</div>

</main>

</div>

<!-- =========================
    MODAL COMPRA
========================= -->

<div
id="modalCompra"
class="modal"
style="display:none;">

<div class="modal-content">

<h3>Datos de compra</h3>

<p>

<b>Producto:</b>

<?php echo $producto['nombre']; ?>

</p>

<p>

<b>Precio:</b>

$<?php echo number_format($producto['precio_venta']); ?>

</p>

<label>Cantidad</label>

<input
type="number"
id="cantidad"
value="1"
min="1"
>

<p>

<b>Total:</b>

$<span id="total"></span>

</p>

<label>Nombre</label>

<input type="text">

<label>Tarjeta</label>

<input type="text">

<label>Fecha</label>

<input type="month">

<label>CVV</label>

<input
type="text"
maxlength="3"
>

<button onclick="comprar()">
Confirmar
</button>

<button onclick="cerrarModal()">
Cancelar
</button>

</div>

</div>

<footer class="footer">

<small>Mariajose © 2026</small>

</footer>

<script>

/* SIDEBAR */

document
.getElementById("menu-toggle")
.addEventListener("click",()=>{

    document
    .getElementById("sidebar")
    .classList.toggle("active");

});

/* MODAL */

function abrirModal(){

    document
    .getElementById("modalCompra")
    .style.display = "block";

    calcular();

}

function cerrarModal(){

    document
    .getElementById("modalCompra")
    .style.display = "none";

}

/* TOTAL */

let precio =
<?php echo $producto['precio_venta']; ?>;

function calcular(){

    let cant =
    document.getElementById("cantidad").value;

    document
    .getElementById("total")
    .textContent =
    (precio * cant).toLocaleString();

}

document
.getElementById("cantidad")
.addEventListener("input", calcular);

calcular();

/* ACCIONES */

function comprar(){

    let cantidad =
    document.getElementById("cantidad").value;

    window.location.href =
    "comprar.php?id=<?php echo $producto['id_producto']; ?>&cantidad="
    + cantidad;

}

function carrito(){

    alert("Agregado al carrito");

}

function alquilar(){

    window.location.href =
    "alquileruser.php?id=<?php echo $producto['id_producto']; ?>";

}

</script>

</body>
</html>