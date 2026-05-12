<?php
session_start();
include("conexion.php");

/* =========================
   VALIDAR SESIÓN
========================= */
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/* =========================
   VALIDAR ROL
========================= */
if ($_SESSION['usuario']['rol'] !== "user") {
    header("Location: dashboard.php");
    exit();
}

$usuario = $_SESSION['usuario'];

/* =========================
   CONSULTAR PRODUCTOS
========================= */
$sql = "SELECT * 
FROM producto
WHERE stock_disponible > 0
ORDER BY id_producto DESC";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Productos - TICKETS-FET</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/productos.css">

<script src="https://kit.fontawesome.com/9d1a86738f.js" crossorigin="anonymous"></script>

</head>

<body>

<header class="header">

<div class="menu">

<button id="menu-toggle">
<i class="fa-solid fa-bars"></i>
</button>

<div class="logo">
<img src="img/logo_dashboard.png" alt="logo">
</div>

</div>

<div class="search">

<input type="text" placeholder="Buscar...">

<i class="fa-solid fa-magnifying-glass"></i>

</div>

<div class="icons">

<button>
<i class="fa-regular fa-bell"></i>
</button>

<form method="POST" action="logout.php" style="display:inline;">

<button type="submit">
<i class="fa-solid fa-arrow-right-from-bracket"></i>
</button>

</form>

</div>

<div class="user">

<div>

<p>
<b><?php echo htmlspecialchars($usuario['nombre']); ?></b>
</p>

<small>Usuario tienda</small>

</div>

<img src="img/avatar.png" alt="avatar">

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

<!-- CONTENIDO -->
<main class="main-content">

<h1>Productos</h1>

<p>
Explora nuestra selección de productos
</p>

<div class="productos-grid">

<?php
if(mysqli_num_rows($resultado) > 0){

while($producto = mysqli_fetch_assoc($resultado)){

$imagen = !empty($producto['imagen']) ? $producto['imagen'] : "avatar.png";
?>

<div class="producto"
onclick="verProducto(<?php echo $producto['id_producto']; ?>)">

<img src="img/<?php echo $imagen; ?>" alt="producto">

<h3>
<?php echo htmlspecialchars($producto['nombre']); ?>
</h3>

<p>
$<?php echo number_format($producto['precio_alquiler'], 0, ',', '.'); ?>
</p>

<small>

Stock disponible:
<?php echo $producto['stock_disponible']; ?>

</small>

</div>

<?php
}

}else{
?>

<p>No hay productos disponibles</p>

<?php } ?>

</div>

</main>

</div>

<footer class="footer">
<small>Mariajose © 2026</small>
</footer>

<script>

/* =========================
   SIDEBAR
========================= */
const btn = document.getElementById("menu-toggle");

const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", ()=>{

sidebar.classList.toggle("active");

});

/* =========================
   VER PRODUCTO
========================= */
function verProducto(id){

window.location.href =
      "productodetalle.php?id=" + id;

}

</script>

</body>
</html>