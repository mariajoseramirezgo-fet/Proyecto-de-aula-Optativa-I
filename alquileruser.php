<?php
session_start();
include("conexion.php");

// VALIDAR SESIÓN
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

// VALIDAR ROL
if ($_SESSION['usuario']['rol'] !== "user") {
    header("Location: dashboard.php");
    exit();
}

/* =========================
   REGISTRAR ALQUILER
========================= */
if(isset($_POST['confirmar_alquiler'])){

    $id_cliente = $_SESSION['usuario']['id_cliente'];
    $id_empleado = 1; 

    $producto = mysqli_real_escape_string($conexion, $_POST['producto']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $total = $_POST['total'];

    $estado = "activo";

    $sql = "INSERT INTO alquiler
    (id_cliente, id_empleado, fecha_inicio, fecha_fin, estado, total, producto)
    VALUES
    ('$id_cliente','$id_empleado','$fecha_inicio','$fecha_fin','$estado','$total','$producto')";

    mysqli_query($conexion, $sql);

    header("Location: historialuser.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Alquiler - TICKETS-FET</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/alquileruser.css">
<script src="https://kit.fontawesome.com/9d1a86738f.js" crossorigin="anonymous"></script>

</head>

<body>

<header class="header">

<div class="menu">
<button id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
<div class="logo"><img src="img/logo_dashboard.png"></div>
</div>

<div class="search">
<input type="text" placeholder="Buscar...">
<i class="fa-solid fa-magnifying-glass"></i>
</div>

<div class="icons">
<button onclick="logout()"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
</div>

<div class="user">
<div>
<p><b><?php echo $_SESSION['usuario']['nombre']; ?></b></p>
<small>Usuario tienda</small>
</div>
<img src="img/avatar.png">
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

<h1>Alquilar producto</h1>

<div class="alquiler-box">

<h2>Información del alquiler</h2>

<form id="formAlquiler" class="form-grid" method="POST">

<div class="campo">
<label>Producto</label>
<input type="text" name="producto" required>
</div>

<div class="campo">
<label>Documento</label>
<input type="text" id="documento" required>
</div>

<div class="campo">
<label>Fecha inicio</label>
<input type="date" name="fecha_inicio" id="fechaInicio" required>
</div>

<div class="campo">
<label>Fecha fin</label>
<input type="date" name="fecha_fin" id="fechaFin" required>
</div>

<div class="campo">
<label>Total</label>
<input type="number" name="total" required>
</div>

<div class="campo">
<label>Días de alquiler</label>
<input type="text" id="dias" readonly>
</div>

<div class="boton">
<button type="submit" name="confirmar_alquiler" class="btn-verde">
Confirmar alquiler
</button>
</div>

</form>

</div>

</main>

</div>

<footer class="footer">
<small>Mariajose © 2026</small>
</footer>

<script>

// SIDEBAR
const btn = document.getElementById("menu-toggle");
const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", ()=>{
sidebar.classList.toggle("active");
});

// LOGOUT
function logout(){
localStorage.removeItem("login");
window.location.href="index.html";
}

// SOLO NUMEROS
const docInput = document.getElementById("documento");

docInput.addEventListener("input", function(){
this.value = this.value.replace(/[^0-9]/g, "");
});

// CALCULAR DIAS
const inicio = document.getElementById("fechaInicio");
const fin = document.getElementById("fechaFin");
const dias = document.getElementById("dias");

function calcularDias(){

if(inicio.value && fin.value){

let f1 = new Date(inicio.value);
let f2 = new Date(fin.value);

let diff = (f2 - f1) / (1000 * 60 * 60 * 24);

dias.value = diff >= 0 ? diff + 1 : "";

}

}

inicio.addEventListener("change", calcularDias);
fin.addEventListener("change", calcularDias);


// VALIDACION
document.getElementById("formAlquiler").addEventListener("submit", function(e){

if(dias.value === ""){
alert("Selecciona fechas válidas");
e.preventDefault();
}

});

</script>

</body>
</html>