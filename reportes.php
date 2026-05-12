<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN REAL
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/* =========================
   CONSULTAS DINÁMICAS
========================= */

// TOTAL PRODUCTOS
$totalProductos = mysqli_fetch_assoc(
mysqli_query($conexion,"
SELECT COUNT(*) AS total 
FROM producto
"))['total'];

// PRODUCTOS CON STOCK BAJO
$productosVendidos = mysqli_fetch_assoc(
mysqli_query($conexion,"
SELECT COUNT(*) AS total 
FROM producto 
WHERE stock_disponible <= 5
"))['total'];

// ALQUILERES ACTIVOS
$alquileresActivos = mysqli_fetch_assoc(
mysqli_query($conexion,"
SELECT COUNT(*) AS total 
FROM alquiler 
WHERE estado='Activo'
"))['total'];

// CRECIMIENTO = TOTAL ALQUILERES
$crecimiento = mysqli_fetch_assoc(
mysqli_query($conexion,"
SELECT COUNT(*) AS total 
FROM alquiler
"))['total'];

/* =========================
    DATOS GRÁFICA
========================= */

$datosGrafica = [];

$queryGrafica = mysqli_query($conexion,"
SELECT DAY(fecha_inicio) as dia,
COUNT(*) as total
FROM alquiler
GROUP BY DAY(fecha_inicio)
ORDER BY dia ASC
LIMIT 7
");

while($fila = mysqli_fetch_assoc($queryGrafica)){
    $datosGrafica[] = $fila['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes- TICKETS-FET</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/reportes.css?v=1">
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
<input type="text" placeholder="Buscar...">
<i class="fa-solid fa-magnifying-glass"></i>
</div>

<div class="icons">
<button onclick="logout()">
<i class="fa-solid fa-arrow-right-from-bracket"></i>
</button>
</div>

<div class="user">
<div>
<p><b><?php echo $_SESSION['usuario']['nombre']; ?></b></p>
<small>Administrador de tienda</small>
</div>
<img src="img/avatar.png">
</div>

</header>

<div class="container">

<aside id="sidebar" class="sidebar">
<ul>
<li><a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a></li>
<li><a href="inventario.php"><i class="fa-solid fa-box"></i><span>Inventario</span></a></li>
<li><a href="alquileres.php"><i class="fa-solid fa-calendar"></i><span>Alquileres</span></a></li>
<li><a href="reportes.php"><i class="fa-solid fa-chart-line"></i><span>Reportes</span></a></li>
<li><a href="tickets.php"><i class="fa-solid fa-ticket"></i><span>Tickets</span></a></li>
<li><a href="clientes.php"><i class="fa-solid fa-users"></i><span>Clientes</span></a></li>
<li><a href="configuracion.php"><i class="fa-solid fa-gear"></i><span>Configuración</span></a></li>
</ul>
</aside>

<main class="main-content">

<h1>Reportes de rendimiento</h1>
<p>Análisis detallado de ventas y alquileres del periodo actual.</p>

<div class="cards">

<div class="card-inventario">
<h3>Ventas totales</h3>
<p><?php echo $totalProductos; ?> productos</p>
<i class="fa-solid fa-boxes-stacked"></i>
</div>

<div class="card-venta">
<h3>Productos vendidos</h3>
<p><?php echo $productosVendidos; ?> productos</p>
<i class="fa-solid fa-circle-check"></i>
</div>

<div class="card-alquilados">
<h3>Alquileres activos</h3>
<p><?php echo $alquileresActivos; ?> productos</p>
<i class="fa-regular fa-calendar"></i>
</div>

<div class="card-crecimiento">
<h3>Crecimiento</h3>
<p><?php echo $crecimiento; ?></p>
<i class="fa-solid fa-arrow-trend-up"></i>
</div>

</div>

<section class="cards-large">

<div class="card-large">
<h3>Actividad de alquileres</h3>
<small>Última semana</small>
<canvas id="graficaVentas"></canvas>
</div>

<div class="card-large">
<h3>Stock bajo</h3>

<?php
$stockBajo = mysqli_query($conexion, "SELECT nombre, stock_disponible FROM producto WHERE stock_disponible <= 5 LIMIT 3");
while($row = mysqli_fetch_assoc($stockBajo)){
?>
<div class="stock-item">
<i class="fa-solid fa-box"></i>
<span><?php echo $row['nombre']; ?></span>
<span class="rojo"><?php echo $row['stock_disponible']; ?></span>
</div>
<?php } ?>

</div>

<div class="card-large">

<h3>Tickets</h3>

<div class="stock-item">
<i class="fa-solid fa-triangle-exclamation rojo"></i>
<span>Balón roto</span>
</div>

<div class="stock-item">
<i class="fa-solid fa-triangle-exclamation amarillo"></i>
<span>Red dañada</span>
</div>

<div class="stock-item">
<i class="fa-solid fa-circle-check verde"></i>
<span>Guantes OK</span>
</div>

</div>

</section>

</main>

</div>

<footer class="footer">
<small>Mariajose &copy; 2026</small>
</footer>

<script>

function logout(){
window.location.href="logout.php";
}

const btn=document.getElementById("menu-toggle");
const sidebar=document.getElementById("sidebar");

btn.addEventListener("click",()=>{
sidebar.classList.toggle("active");
});

// Gráfica
const ctx=document.getElementById('graficaVentas');

new Chart(ctx,{
type:'line',
data:{
labels:['Lun','Mar','Mie','Jue','Vie','Sab','Dom'],
datasets:[{
label:'Alquileres',
data: <?php echo json_encode($datosGrafica); ?>,
borderColor:'#4a6cf7',
backgroundColor:'rgba(74,108,247,0.1)',
tension:0.4,
fill:true
}]
},
options:{
responsive:true,
plugins:{legend:{display:false}}
}
});

</script>

</body>
</html>