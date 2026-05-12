<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'] ?? 0;
$cantidad = $_GET['cantidad'] ?? 1;

$sql = "SELECT * FROM inventario WHERE id_producto = $id";
$res = mysqli_query($conexion, $sql);
$inv = mysqli_fetch_assoc($res);

if($inv['stock_actual'] < $cantidad){
    die("Stock insuficiente");
}

mysqli_query($conexion,"
UPDATE inventario
SET stock_actual = stock_actual - $cantidad
WHERE id_producto = $id
");

$id_usuario = $_SESSION['usuario']['id_usuario'] ?? 0;

mysqli_query($conexion,"
INSERT INTO movimiento_inventario
(id_producto, id_usuario, tipo, cantidad, fecha, descripcion)
VALUES
($id, $id_usuario, 'salida', $cantidad, NOW(), 'Compra desde detalle de producto')
");

header("Location: producto.php");

?>