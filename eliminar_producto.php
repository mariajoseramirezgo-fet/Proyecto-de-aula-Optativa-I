<?php
include("conexion.php");

if(!isset($_GET['id'])){
    header("Location: inventario.php");
    exit();
}

$id = $_GET['id'];

/* BORRAR INVENTARIO PRIMERO (IMPORTANTE POR FK) */
mysqli_query($conexion, "DELETE FROM inventario WHERE id_producto=$id");

/* BORRAR PRODUCTO */
mysqli_query($conexion, "DELETE FROM producto WHERE id_producto=$id");

header("Location: inventario.php");
exit();
?>