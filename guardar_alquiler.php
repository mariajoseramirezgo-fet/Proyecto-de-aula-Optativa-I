<?php
session_start();
require "sporthub.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

$id_cliente = $_POST['id_cliente'];
$id_producto = $_POST['id_producto'];
$cantidad = $_POST['cantidad'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

// calcular días
$inicio = new DateTime($fecha_inicio);
$fin = new DateTime($fecha_fin);
$dias = $inicio->diff($fin)->days + 1;

//  precio producto
$p = $conn->query("SELECT precio_venta FROM producto WHERE id_producto=$id_producto")
            ->fetch_assoc();

$precio = $p['precio_venta'];

// subtotal
$subtotal = $precio * $cantidad;

// ===============================
// 1. INSERTAR ALQUILER
// ===============================
$conn->query("
INSERT INTO alquiler (id_cliente, id_empleado, fecha_inicio, fecha_fin, estado, total)
VALUES ($id_cliente, 1, '$fecha_inicio', '$fecha_fin', 'activo', $subtotal)
");

$id_alquiler = $conn->insert_id;

// ===============================
// 2. INSERTAR DETALLE
// ===============================
$conn->query(" 
INSERT INTO detalle_alquiler 
(id_alquiler, id_producto, cantidad, precio_alquiler, subtotal)
VALUES 
($id_alquiler, $id_producto, $cantidad, $precio, $subtotal)
");

// ===============================
echo "<script>
alert('Alquiler registrado correctamente');
window.location.href='userinicio.php';
</script>";
?>