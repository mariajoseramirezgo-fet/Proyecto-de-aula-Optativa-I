<?php
session_start();
include("conexion.php");

$correo = $_POST['correo'];
$password = $_POST['password'];

/* =========================
   BUSCAR EMPLEADO
========================= */
$sql_emp = "SELECT * FROM empleado 
WHERE correo='$correo' 
AND password='$password'";

$resultado_emp = mysqli_query($conexion, $sql_emp);

if(mysqli_num_rows($resultado_emp) > 0){

    $empleado = mysqli_fetch_assoc($resultado_emp);

    $_SESSION['usuario'] = [
        "id_empleado" => $empleado['id_empleado'],
        "nombre" => $empleado['nombre'],
        "rol" => "admin"
    ];
    header("Location: dashboard.php");
    exit();
}

/* =========================
   BUSCAR CLIENTE
========================= */
$sql_cli = "SELECT * FROM cliente 
WHERE correo='$correo' 
AND password='$password'";

$resultado_cli = mysqli_query($conexion, $sql_cli);

if(mysqli_num_rows($resultado_cli) > 0){

    $cliente = mysqli_fetch_assoc($resultado_cli);

    $_SESSION['usuario'] = [
        "id_cliente" => $cliente['id_cliente'],
        "nombre" => $cliente['nombre'],
        "rol" => "user"
    ];

    header("Location: userinicio.php");
    exit();
}

/* =========================
   ERROR LOGIN
========================= */
echo "Correo o contraseña incorrectos";
?>