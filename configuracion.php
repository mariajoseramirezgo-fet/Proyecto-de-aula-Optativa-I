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
   FUNCIÓN NOTIFICACIONES
========================= */
function guardarNotificacion($conexion, $mensaje){

    $mensaje = mysqli_real_escape_string(
        $conexion,
        $mensaje
    );

    mysqli_query(
        $conexion,
        "INSERT INTO notificaciones
        (mensaje, fecha)

        VALUES

        ('$mensaje', NOW())"
    );
}

/* =========================
   GUARDAR VENDEDOR
========================= */
if(isset($_POST['guardar_vendedor'])){

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password']
    );

    $rol = "vendedor";

    $sql = "INSERT INTO empleado
    (nombre, correo, password, rol)

    VALUES

    ('$nombre','$correo','$password','$rol')";

    if(!mysqli_query($conexion,$sql)){
        die("Error: " . mysqli_error($conexion));
    }

    guardarNotificacion(
        $conexion,
        "Se agregó un nuevo vendedor: $nombre"
    );

    header("Location: configuracion.php");
    exit();
}

/* =========================
   ELIMINAR VENDEDOR
========================= */
if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    $consulta = mysqli_query(
        $conexion,
        "SELECT nombre
        FROM empleado
        WHERE id_empleado='$id'"
    );

    $datos = mysqli_fetch_assoc($consulta);

    $nombreVendedor = $datos['nombre'];

    mysqli_query(
        $conexion,
        "DELETE FROM empleado
        WHERE id_empleado='$id'"
    );

    guardarNotificacion(
        $conexion,
        "Se eliminó el vendedor: $nombreVendedor"
    );

    header("Location: configuracion.php");
    exit();
}

/* =========================
   ACTUALIZAR CUENTA
========================= */
if(isset($_POST['guardar_cuenta'])){

    $id_usuario =
    $_SESSION['usuario']['id_empleado'] ?? 0;

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre_cuenta']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo_cuenta']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password_cuenta']
    );

    $sqlCuenta = "UPDATE empleado SET

    nombre = '$nombre',
    correo = '$correo',
    password = '$password'

    WHERE id_empleado = '$id_usuario'";

    mysqli_query($conexion, $sqlCuenta);

    $_SESSION['usuario']['nombre'] = $nombre;
    $_SESSION['usuario']['correo'] = $correo;

    guardarNotificacion(
        $conexion,
        "Se actualizaron los datos de la cuenta administrador"
    );

    header("Location: configuracion.php");
    exit();
}

/* =========================
   CAMBIAR CONTRASEÑA
========================= */
if(isset($_POST['cambiar_password'])){

    $id_usuario =
    $_SESSION['usuario']['id_empleado'] ?? 0;

    $nueva = mysqli_real_escape_string(
        $conexion,
        $_POST['nueva_password']
    );

    $sqlPass = "UPDATE empleado SET

    password = '$nueva'

    WHERE id_empleado = '$id_usuario'";

    mysqli_query($conexion, $sqlPass);

    guardarNotificacion(
        $conexion,
        "Se cambió la contraseña del administrador"
    );

    header("Location: configuracion.php");
    exit();
}

/* =========================
   CONSULTAR VENDEDORES
========================= */
$vendedores = mysqli_query(
    $conexion,
    "SELECT * FROM empleado
    WHERE rol='vendedor'"
);

/* =========================
   CONSULTAR NOTIFICACIONES
========================= */
$notificaciones = mysqli_query(
    $conexion,
    "SELECT * FROM notificaciones
    ORDER BY id_notificacion DESC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Configuración - SportHub</title>

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="css/configuracion.css?v=2">

<script src="https://kit.fontawesome.com/9d1a86738f.js"
crossorigin="anonymous"></script>

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

<p>
<b>
<?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? ''); ?>
</b>
</p>

<small>Administrador de tienda</small>

</div>

<img src="img/avatar.png">

</div>

</header>

<div class="container">

<!-- SIDEBAR -->

<aside class="sidebar" id="sidebar">

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

<main class="main">

<h1>CONFIGURACIÓN</h1>

<p>
Administra tu cuenta,
usuarios y preferencias del sistema.
</p>

<div class="config-container">

<!-- MENU -->

<div class="config-menu">

<ul>

<li data-seccion="cuenta" class="activo">
Cuenta
</li>

<li data-seccion="vendedores">
Vendedores
</li>

<li data-seccion="notificaciones">
Notificaciones
</li>

<li data-seccion="seguridad">
Seguridad
</li>

</ul>

</div>

<!-- CONTENIDO -->

<div class="config-content">

<!-- CUENTA -->

<div id="cuenta" class="seccion">

<h2>ADMINISTRAR CUENTA</h2>

<div class="perfil-card">

<img src="img/avatar.png">

<div class="perfil-form">

<form method="POST">

<label>Nombre</label>

<input
type="text"
name="nombre_cuenta"
value="<?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? ''); ?>"
required>

<label>Correo</label>

<input
type="email"
name="correo_cuenta"
value="<?php echo htmlspecialchars($_SESSION['usuario']['correo'] ?? ''); ?>"
required>

<label>Contraseña</label>

<input
type="password"
name="password_cuenta"
required>

<button
class="btn-verde"
name="guardar_cuenta">

Guardar cambios

</button>

</form>

</div>

</div>

</div>

<!-- VENDEDORES -->

<div id="vendedores" class="seccion oculto">

<h2>GESTIÓN DE VENDEDORES</h2>

<button
class="btn-verde"
onclick="abrirFormulario()">

+ Designar vendedor

</button>

<table class="tabla">

<tr>

<th>Nombre</th>

<th>Correo</th>

<th>Rol</th>

<th>Acción</th>

</tr>

<tbody>

<?php while($v = mysqli_fetch_assoc($vendedores)) { ?>

<tr>

<td>
<?php echo htmlspecialchars($v['nombre']); ?>
</td>

<td>
<?php echo htmlspecialchars($v['correo']); ?>
</td>

<td>
<?php echo htmlspecialchars($v['rol']); ?>
</td>

<td>

<a href="configuracion.php?eliminar=<?php echo $v['id_empleado']; ?>"

onclick="return confirm(
'¿Seguro deseas eliminar?'
)"

class="btn-rojo">

Eliminar

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<div id="formVendedor"
class="formulario oculto">

<h3>Nuevo vendedor</h3>

<form method="POST">

<input
type="text"
name="nombre"
placeholder="Nombre"
required>

<input
type="email"
name="correo"
placeholder="Correo"
required>

<input
type="password"
name="password"
placeholder="Contraseña"
required>

<button
class="btn-verde"
name="guardar_vendedor">

Guardar

</button>

</form>

</div>

</div>

<!-- NOTIFICACIONES -->

<div id="notificaciones"
class="seccion oculto">

<h2>NOTIFICACIONES</h2>

<?php while($n = mysqli_fetch_assoc($notificaciones)) { ?>

<div class="alerta verde">

✔ <?php echo htmlspecialchars($n['mensaje']); ?>

<br>

<small>
<?php echo $n['fecha']; ?>
</small>

</div>

<?php } ?>

</div>

<!-- SEGURIDAD -->

<div id="seguridad"
class="seccion oculto">

<h2>SEGURIDAD</h2>

<form method="POST">

<label>Nueva contraseña</label>

<input
type="password"
name="nueva_password"
required>

<button
class="btn-verde"
name="cambiar_password">

Actualizar contraseña

</button>

</form>

</div>

</div>

</div>

</main>

</div>

<footer class="footer">

<small>Mariajose © 2026</small>

</footer>

<script>

/* SIDEBAR */

document.getElementById("menu-toggle")
.addEventListener("click", ()=>{

document.getElementById("sidebar")
.classList.toggle("active");

});

/* LOGOUT */

function logout(){

window.location.href="logout.php";

}

/* SECCIONES */

const items =
document.querySelectorAll(".config-menu li");

const secciones =
document.querySelectorAll(".seccion");

items.forEach(item=>{

item.addEventListener("click", ()=>{

secciones.forEach(sec=>{

sec.classList.add("oculto");

});

items.forEach(i=>{

i.classList.remove("activo");

});

document
.getElementById(item.dataset.seccion)
.classList.remove("oculto");

item.classList.add("activo");

});

});

/* FORM VENDEDOR */

function abrirFormulario(){

document.getElementById("formVendedor")
.classList.toggle("oculto");

}

</script>

</body>
</html>