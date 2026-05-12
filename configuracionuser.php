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

$usuario = $_SESSION['usuario'];
$id_usuario = $usuario['id_cliente'];

// CAMBIAR CONTRASEÑA
if(isset($_POST['cambiar_password'])){

    $actual = $_POST['actual'];
    $nueva = $_POST['nueva'];

    $sql = "SELECT password FROM cliente WHERE id_cliente='$id_usuario'";
    $res = mysqli_query($conexion,$sql);
    $row = mysqli_fetch_assoc($res);

    if($actual == $row['password']){

        $update = "UPDATE cliente SET password='$nueva' WHERE id_cliente='$id_usuario'";
        mysqli_query($conexion,$update);

        echo "<script>alert('Contraseña actualizada correctamente');</script>";

    }else{
        echo "<script>alert('Contraseña actual incorrecta');</script>";
    }
}

// CAMBIAR FOTO
if(isset($_POST['guardar_foto'])){

    $nombreImg = $_FILES['foto']['name'];
    $ruta = "img/".$nombreImg;

    move_uploaded_file($_FILES['foto']['tmp_name'],$ruta);

    $sql = "UPDATE cliente SET foto='$ruta' WHERE id_cliente='$id_usuario'";
    mysqli_query($conexion,$sql);

    echo "<script>alert('Foto actualizada');</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Configuración_user - TICKETS-FET</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/configuracionuser.css">
<script src="https://kit.fontawesome.com/9d1a86738f.js" crossorigin="anonymous"></script>
</head>

<body>

<header class="header">

<div class="menu">
<button id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
<div class="logo"><img src="img/logo_dashboard.png" alt="logo"></div>
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
<p><b id="nombreUser"><?php echo $usuario['nombre']; ?></b></p>
<small>Usuario tienda</small>
</div>
<img id="imgUser" src="img/avatar.png" alt="avatar">
</div>

</header>

<div class="container">

<aside id="sidebar" class="sidebar">
<ul>
<li><a href="userinicio.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a></li>
<li><a href="producto.php"><i class="fa-solid fa-box"></i><span>Productos</span></a></li>
<li><a href="alquileruser.php"><i class="fa-solid fa-calendar"></i><span>Alquiler</span></a></li>
<li><a href="configuracionuser.php"><i class="fa-solid fa-gear"></i><span>Configuración</span></a></li>
</ul>
</aside>

<main class="main-content">

<h2>Configuración</h2>

<div class="config-container">

<!-- PERFIL -->
<div class="card">
<h3>Perfil</h3>

<form method="POST" enctype="multipart/form-data">

<img id="previewImg" src="img/avatar.png" class="avatar">

<input type="file" name="foto" required>

<button type="submit" name="guardar_foto">
Guardar imagen
</button>

</form>

</div>

<!-- CAMBIAR CONTRASEÑA -->
<div class="card">
<h3>Cambiar contraseña</h3>

<form method="POST">

<input type="password" name="actual" placeholder="Contraseña actual" required>

<input type="password" name="nueva" placeholder="Nueva contraseña" required>

<button type="submit" name="cambiar_password">
Actualizar contraseña
</button>

</form>

</div>

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
    window.location.href="logout.php";
}

</script>

</body>
</html>