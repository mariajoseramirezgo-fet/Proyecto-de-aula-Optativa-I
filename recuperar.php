<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña - TICKETS-FET</title>

<link rel="stylesheet" href="css/mediaquerys.css">
<link rel="stylesheet" href="css/default.css">
<link rel="shortcut icon" href="img/icon.png">
</head>

<body>

<img src="img/logo.png" alt="Logo" class="logo-login">

<main>
<div class="card">

<form action="recuperar2.php" method="POST" class="form-login">

<h2>RECUPERAR CONTRASEÑA</h2>
<p>Ingresa tu correo para recuperar tu cuenta</p>

<input class="input-login" type="email" name="correo" placeholder="Correo electrónico" required>

<input class="btn-enviar" type="submit" value="ENVIAR">

<div class="extra-links">
<p>¿Recordaste tu contraseña? <a href="index.php">Inicia sesión</a></p>
</div>

</form>

</div>
</main>

<footer>
<small>Mariajose &copy; 2026</small>
</footer>

</body>
</html>