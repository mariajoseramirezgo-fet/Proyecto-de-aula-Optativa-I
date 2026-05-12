<?php
session_start();

/* =========================
    VALIDACIÓN ADMIN
========================= */

if (
    !isset($_SESSION['usuario']) ||
    $_SESSION['usuario']['rol'] != 'admin'
){
    header("Location: index.php");
    exit();
}

include("conexion.php");

/* =========================
    TOTAL PRODUCTOS
========================= */

$sqlProductos = "
SELECT COUNT(*) as total
FROM producto
";

$resProductos = mysqli_query($conexion, $sqlProductos);

$totalProductos = mysqli_fetch_assoc($resProductos)['total'] ?? 0;

/* =========================
    DISPONIBLES
========================= */

$sqlDisponibles = "
SELECT SUM(stock_disponible) as total
FROM producto
";

$resDisponibles = mysqli_query($conexion, $sqlDisponibles);

$totalDisponibles = mysqli_fetch_assoc($resDisponibles)['total'] ?? 0;

/* =========================
   ALQUILADOS
========================= */

$sqlAlquilados = "
SELECT SUM(stock_total - stock_disponible) as total
FROM producto
";

$resAlquilados = mysqli_query($conexion, $sqlAlquilados);

$totalAlquilados = mysqli_fetch_assoc($resAlquilados)['total'] ?? 0;

/* =========================
   TOTAL TICKETS
========================= */

$sqlTickets = "
SELECT COUNT(*) as total
FROM ticket
";

$resTickets = mysqli_query($conexion, $sqlTickets);

$totalTickets = mysqli_fetch_assoc($resTickets)['total'] ?? 0;

/* =========================
   DATOS GRÁFICA ALQUILERES
========================= */

$datosGrafica = [];

$sqlGrafica = "
SELECT
DAYNAME(fecha_inicio) as dia,
COUNT(*) as total
FROM alquiler
WHERE fecha_inicio >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DAY(fecha_inicio)
";

$resGrafica = mysqli_query($conexion, $sqlGrafica);

/* ARRAY BASE */

$dias = [
    "Monday" => 0,
    "Tuesday" => 0,
    "Wednesday" => 0,
    "Thursday" => 0,
    "Friday" => 0,
    "Saturday" => 0,
    "Sunday" => 0
];

/* LLENAR DATOS */

while ($fila = mysqli_fetch_assoc($resGrafica)) {
    $dias[$fila['dia']] = $fila['total'];
}

/* ARRAY FINAL */

$datosGrafica = [
    $dias['Monday'],
    $dias['Tuesday'],
    $dias['Wednesday'],
    $dias['Thursday'],
    $dias['Friday'],
    $dias['Saturday'],
    $dias['Sunday']
];

/* =========================
   STOCK BAJO
========================= */

$sqlStock = "
SELECT nombre, stock_disponible
FROM producto
WHERE stock_disponible <= 5
LIMIT 5
";

$resStock = mysqli_query($conexion, $sqlStock);

/* =========================
   TICKETS RECIENTES
========================= */

$sqlTicketLista = "
SELECT descripcion, estado
FROM ticket
ORDER BY id_ticket DESC
LIMIT 5
";

$resTicketLista = mysqli_query($conexion, $sqlTicketLista);
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Dashboard - SportHub</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/dashboard.css?v=1">

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

    <a href="logout.php">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
    </a>

    <div class="user">

        <div>
            <p><b><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></b></p>
            <small>Administrador</small>
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

<h1>Dashboard</h1>

<p>Monitoreo general del sistema de inventario y alquileres.</p>

<div class="cards">

<div class="card-inventario">
<h3>Inventario Total</h3>
<p><?php echo $totalProductos; ?> productos</p>
<i class="fa-solid fa-boxes-stacked"></i>
</div>

<div class="card-venta">
<h3>Stock Disponible</h3>
<p><?php echo $totalDisponibles; ?> productos</p>
<i class="fa-solid fa-circle-check"></i>
</div>

<div class="card-alquilados">
<h3>Alquilados</h3>
<p><?php echo $totalAlquilados; ?> productos</p>
<i class="fa-regular fa-calendar"></i>
</div>

<div class="card-tickets">
<h3>Tickets</h3>
<p><?php echo $totalTickets; ?> registrados</p>
<i class="fa-solid fa-triangle-exclamation"></i>
</div>

</div>

<section class="cards-large">

<div class="card-large">
<h3>Actividad de alquileres</h3>
<small>Últimos 7 días</small>
<canvas id="graficaVentas"></canvas>
</div>

<div class="card-large">

<h3>Stock bajo</h3>

<?php while ($stock = mysqli_fetch_assoc($resStock)) { ?>

<div class="stock-item">
<i class="fa-solid fa-box"></i>
<span><?php echo $stock['nombre']; ?></span>
<span class="rojo"><?php echo $stock['stock_disponible']; ?></span>
</div>

<?php } ?>

</div>

<div class="card-large">

<h3>Tickets recientes</h3>

<?php while ($ticket = mysqli_fetch_assoc($resTicketLista)) { ?>

<div class="stock-item">
<i class="fa-solid fa-ticket"></i>
<span><?php echo $ticket['descripcion']; ?></span>
</div>

<?php } ?>

</div>

</section>

</main>

</div>

<footer class="footer">
<small>Mariajose © 2026</small>
</footer>

<script>
const btn = document.getElementById("menu-toggle");
const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

const ctx = document.getElementById('graficaVentas');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'],
        datasets: [{
            label: 'Alquileres',
            data: <?php echo json_encode($datosGrafica); ?>,
            borderColor: '#4a6cf7',
            backgroundColor: 'rgba(74,108,247,0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

});
</script>

</body>
</html>