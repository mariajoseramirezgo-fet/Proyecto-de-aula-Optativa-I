<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - TICKETS-FET</title>

<link rel="stylesheet" href="css/mediaquerys.css?=2">
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/default.css?v=1">
<link rel="shortcut icon" href="img/icon.png">
</head>

<body>

<img src="img/logo.png" alt="Logo" class="logo-login">

<main>
<div class="card">

<form action="guardar.php" method="POST" class="form-login">

<h2>REGÍSTRATE</h2>
<p>Crea una cuenta nueva</p>

<!-- NOMBRE -->
<input class="input-login" type="text" name="nombre" placeholder="Nombre" required>

<!-- APELLIDO -->
<input class="input-login" type="text" name="apellido" placeholder="Apellido" required>

<!-- CORREO -->
<input class="input-login" type="email" name="correo" placeholder="Correo electrónico" required>

<!-- TELÉFONO -->
<input class="input-login" type="text" name="telefono" placeholder="Teléfono">

<!-- CONTRASEÑA -->
<input class="input-login" type="password" name="password" placeholder="Contraseña" required>


<input class="btn-enviar" type="submit" value="REGISTRARSE">

<div class="extra-links">
<p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión</a></p>
</div>

</form>

</div>
</main>

<footer>
<small>Mariajose &copy; 2026</small>
</footer>

</body>
</html>