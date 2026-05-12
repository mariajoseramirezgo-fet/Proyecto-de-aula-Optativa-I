<?php
include("conexion.php");

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$password = $_POST['password'];
$rol = $_POST['rol']; // viene del formulario

if($rol == "admin"){

    // GUARDAR EN EMPLEADO
    $sql = "INSERT INTO empleado (nombre, correo, password)
            VALUES ('$nombre', '$correo', '$password')";

} else {

    // GUARDAR EN CLIENTE
    $sql = "INSERT INTO cliente (nombre, apellido, correo, telefono, password)
            VALUES ('$nombre', '$apellido', '$correo', '$telefono', '$password')";
}

if(mysqli_query($conexion, $sql)){
    echo "Registro exitoso";
} else {
    echo "Error: " . mysqli_error($conexion);
}
?>