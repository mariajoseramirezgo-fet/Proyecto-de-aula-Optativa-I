<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TICKETS-FET</title>

<link rel="stylesheet" href="css/default.css">
<link rel="shortcut icon" href="img/icon.png">
</head>

<body>

<img src="img/logo.png" alt="Logo" class="logo-login">

<main>
<div class="card">

<form method="POST" action="validar.php" class="form-login">

<h2>INICIO SESIÓN</h2>
<p>Ingresa tus credenciales para acceder</p>

<input name="correo" class="input-login" type="email" placeholder="Correo electrónico" required>
<input name="password" class="input-login" type="password" placeholder="Contraseña" required>

<div class="remember">
<input type="checkbox">
<label>Mantener la sesión iniciada</label>
</div>

<button type="submit" class="btn-enviar">INICIA SESIÓN</button>

<div class="extra-links">
<p>¿Olvidaste tu contraseña? <a href="recuperar.php">Recuperar</a></p>
<p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
</div>

</form>
</div>
</main>

<footer>
<small>Mariajose © 2026</small>
</footer>

</body>
</html>