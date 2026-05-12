<?php

session_start();

include("conexion.php");

// VALIDAR SESIÓN
if(!isset($_SESSION['usuario'])){

    die("No hay sesión");

}

// ID USUARIO
$id_usuario = $_SESSION['usuario']['id'];

// DATOS DE PRUEBA
$tipo = "alquiler";
$producto = "PlayStation 5";
$inicio = date("Y-m-d");
$fin = "2026-05-20";
$dias = 5;
$total = 150000;
$estado = "activo";

// INSERTAR
$sql = "INSERT INTO historial
(id_usuario, tipo, producto, inicio, fin, dias, total, estado)

VALUES
('$id_usuario', '$tipo', '$producto',
'$inicio', '$fin', '$dias', '$total', '$estado')";

// EJECUTAR
if(mysqli_query($conexion, $sql)){

    echo "Historial guardado correctamente";

}else{

    echo "Error: " . mysqli_error($conexion);

}

?>