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
if ($_SESSION['usuario']['rol'] != "user") {
   header("Location: dashboard.php");
   exit();
}

/* =========================
   DATOS USUARIO
========================= */
$usuario = $_SESSION['usuario'];

$id_usuario = $_SESSION['usuario']['id_cliente'];

/* =========================
   PRODUCTOS ALQUILADOS
========================= */
$sqlAlquilados = "SELECT COUNT(*) AS total
FROM alquiler
WHERE id_cliente = '$id_usuario'
AND estado = 'Activo'";

$resAlquilados = mysqli_query($conexion, $sqlAlquilados);
$rowAlquilados = mysqli_fetch_assoc($resAlquilados);

$totalAlquilados = $rowAlquilados['total'] ?? 0;

/* =========================
   HISTORIAL
========================= */
$sqlHistorial = "SELECT COUNT(*) AS total
FROM alquiler
WHERE id_cliente = '$id_usuario'";

$resHistorial = mysqli_query($conexion, $sqlHistorial);
$rowHistorial = mysqli_fetch_assoc($resHistorial);

$totalHistorial = $rowHistorial['total'] ?? 0;

/* =========================
   PENDIENTES
========================= */
$sqlPendientes = "SELECT COUNT(*) AS total
FROM alquiler
WHERE id_cliente = '$id_usuario'
AND estado = 'Pendiente'";

$resPendientes = mysqli_query($conexion, $sqlPendientes);
$rowPendientes = mysqli_fetch_assoc($resPendientes);

$totalPendientes = $rowPendientes['total'] ?? 0;

/* =========================
   PRÓXIMOS ALQUILERES
========================= */
$sqlProximos = "SELECT 
producto.nombre,
alquiler.fecha_fin

FROM alquiler

INNER JOIN detalle_alquiler 
ON alquiler.id_alquiler = detalle_alquiler.id_alquiler

INNER JOIN producto 
ON detalle_alquiler.id_producto = producto.id_producto

WHERE alquiler.id_cliente = '$id_usuario'
AND alquiler.estado = 'Activo'

ORDER BY alquiler.fecha_fin ASC
LIMIT 5";

$resProximos = mysqli_query($conexion, $sqlProximos);

/* =========================
   ACTIVIDAD RECIENTE
========================= */
$sqlActividad = "SELECT 
producto.nombre,
alquiler.estado

FROM alquiler

INNER JOIN detalle_alquiler 
ON alquiler.id_alquiler = detalle_alquiler.id_alquiler

INNER JOIN producto 
ON detalle_alquiler.id_producto = producto.id_producto

WHERE alquiler.id_cliente = '$id_usuario'

ORDER BY alquiler.id_alquiler DESC
LIMIT 5";

$resActividad = mysqli_query($conexion, $sqlActividad);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Inicio Usuario - TICKETS-FET</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/userinicio.css">

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

<small>Usuario</small>

</div>

<img src="img/avatar.png" alt="avatar">

</div>

</header>

<div class="container">

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

<main class="main-content">

<h1>Bienvenido</h1>

<p>
Gestiona tus alquileres y actividad fácilmente.
</p>

<!-- CARDS -->
<div class="cards">

<div class="card">

<h3>Productos alquilados</h3>

<p>
<?php echo $totalAlquilados; ?>
</p>

<i class="fa-solid fa-box"></i>

</div>

<div class="card">

<h3>Historial</h3>

<p>
<?php echo $totalHistorial; ?>
</p>

<i class="fa-solid fa-clock"></i>

</div>

<div class="card">

<h3>Pendientes</h3>

<p>
<?php echo $totalPendientes; ?>
</p>

<i class="fa-solid fa-triangle-exclamation"></i>

</div>

</div>

<!-- CARDS GRANDES -->
<div class="cards-large">

<!-- PRÓXIMOS -->
<div class="card-large">

<h3>Próximos alquileres</h3>

<?php
if(mysqli_num_rows($resProximos) > 0){

while($fila = mysqli_fetch_assoc($resProximos)){
?>

<div class="item">

<span>
<?php echo htmlspecialchars($fila['nombre']); ?>
</span>

<span>
<?php echo date("d/m/Y", strtotime($fila['fecha_fin'])); ?>
</span>

</div>

<?php
}

}else{
?>

<div class="item">
No tienes alquileres activos
</div>

<?php } ?>

</div>

<!-- ACTIVIDAD -->
<div class="card-large">

<h3>Actividad reciente</h3>

<?php
if(mysqli_num_rows($resActividad) > 0){

while($fila = mysqli_fetch_assoc($resActividad)){
?>

<div class="item">

✔ Producto:
<?php echo htmlspecialchars($fila['nombre']); ?>

- Estado:
<?php echo htmlspecialchars($fila['estado']); ?>

</div>

<?php
}

}else{
?>

<div class="item">
No hay actividad reciente
</div>

<?php } ?>

</div>

<!-- ACCESOS -->
<div class="card-large">

<h3>Accesos rápidos</h3>

<a href="producto.php" class="btn-verde">
Ver productos
</a>

<a href="alquileruser.php" class="btn-verde">
Nuevo alquiler
</a>

</div>

</div>

</main>

</div>

<footer class="footer">
<small>Mariajose © 2026</small>
</footer>

<script>

const btn = document.getElementById("menu-toggle");

const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", ()=>{

sidebar.classList.toggle("active");

});

</script>

</body>
</html>