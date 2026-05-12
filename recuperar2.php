<?php
include("conexion.php");

$correo = $_POST['correo'];

$sql = "SELECT * FROM cliente WHERE correo='$correo'";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva contraseña</title>

<link rel="stylesheet" href="css/default.css">
<link rel="stylesheet" href="css/mediaquerys.css">
<link rel="stylesheet" href="css/recuperar.css">
</head>

<body>

<img src="img/logo.png" alt="Logo" class="logo-login">

<main>
<div class="card">

<?php
if(mysqli_num_rows($resultado) > 0){
?>

<h2>Nueva contraseña</h2>
<p>Ingresa tu nueva contraseña</p>

<form method="POST" action="actualizar_password.php">

    <input type="hidden" name="correo" value="<?php echo $correo; ?>">

    <input class="input-login" type="password" name="nueva" placeholder="Nueva contraseña" required>

    <input class="input-login" type="password" name="confirmar" placeholder="Confirmar contraseña" required>

    <button class="btn-enviar" type="submit">Actualizar</button>

</form>

<?php
} else {
    echo "<p>El correo no existe</p>";
    echo "<a href='recuperar.php'>Volver</a>";
}
?>

</div>
</main>

<footer>
<small>Mariajose © 2026</small>
</footer>

</body>
</html>