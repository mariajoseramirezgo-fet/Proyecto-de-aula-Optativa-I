<?php
include("conexion.php");

$correo = $_POST['correo'];
$nueva = $_POST['nueva'];
$confirmar = $_POST['confirmar'];

if($nueva != $confirmar){
    echo "Las contraseñas no coinciden";
    exit();
}

$sql = "UPDATE cliente SET password='$nueva' WHERE correo='$correo'";

if(mysqli_query($conexion, $sql)){
    echo "Contraseña actualizada <br><a href='index.php'>Ir al login</a>";
} else {
    echo "Error al actualizar";
}
?>