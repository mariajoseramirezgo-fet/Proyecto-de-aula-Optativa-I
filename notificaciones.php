<?php
session_start();
include("conexion.php");

/* =========================
   VALIDAR SESIÓN
========================= */
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

/* =========================
   GUARDAR VENDEDOR
========================= */
if(isset($_POST['guardar_vendedor'])){

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password']
    );

    $rol = "vendedor";

    $sql = "INSERT INTO empleado
    (nombre, correo, password, rol)

    VALUES

    ('$nombre','$correo','$password','$rol')";

    if(!mysqli_query($conexion,$sql)){
        die("Error: " . mysqli_error($conexion));
    }

    /* =========================
       GUARDAR NOTIFICACIÓN
    ========================= */

    $mensaje =
    "Se asignó un nuevo vendedor: $nombre";

    mysqli_query($conexion,"
    INSERT INTO notificaciones
    (mensaje,tipo,fecha)

    VALUES

    ('$mensaje','vendedor',NOW())
    ");

    header("Location: configuracion.php");
    exit();
}

/* =========================
   ELIMINAR VENDEDOR
========================= */
if(isset($_GET['eliminar'])){

    $id = $_GET['eliminar'];

    /* BUSCAR NOMBRE */
    $buscar = mysqli_query($conexion,"
    SELECT nombre
    FROM empleado
    WHERE id_empleado='$id'
    ");

    $data = mysqli_fetch_assoc($buscar);

    $nombreVendedor = $data['nombre'];

    mysqli_query(
        $conexion,
        "DELETE FROM empleado
        WHERE id_empleado='$id'"
    );

    /* =========================
       NOTIFICACIÓN
    ========================= */

    $mensaje =
    "Se eliminó el vendedor: $nombreVendedor";

    mysqli_query($conexion,"
    INSERT INTO notificaciones
    (mensaje,tipo,fecha)

    VALUES

    ('$mensaje','eliminar',NOW())
    ");

    header("Location: configuracion.php");
    exit();
}

/* =========================
   ACTUALIZAR CUENTA
========================= */
if(isset($_POST['guardar_cuenta'])){

    $id_usuario =
    $_SESSION['usuario']['id_usuario'];

    $nombre = mysqli_real_escape_string(
        $conexion,
        $_POST['nombre']
    );

    $correo = mysqli_real_escape_string(
        $conexion,
        $_POST['correo']
    );

    $password = mysqli_real_escape_string(
        $conexion,
        $_POST['password']
    );

    $sqlCuenta = "UPDATE usuario SET

    nombre = '$nombre',
    correo = '$correo',
    password = '$password'

    WHERE id_usuario = '$id_usuario'";

    mysqli_query($conexion, $sqlCuenta);

    $_SESSION['usuario']['nombre'] = $nombre;
    $_SESSION['usuario']['correo'] = $correo;

    /* =========================
       NOTIFICACIÓN
    ========================= */

    $mensaje =
    "Se actualizó la cuenta del administrador";

    mysqli_query($conexion,"
    INSERT INTO notificaciones
    (mensaje,tipo,fecha)

    VALUES

    ('$mensaje','cuenta',NOW())
    ");

    header("Location: configuracion.php");
    exit();
}

/* =========================
   CAMBIAR CONTRASEÑA
========================= */
if(isset($_POST['cambiar_password'])){

    $id_usuario =
    $_SESSION['usuario']['id_usuario'];

    $nueva = mysqli_real_escape_string(
        $conexion,
        $_POST['nueva_password']
    );

    $sqlPass = "UPDATE usuario SET

    password = '$nueva'

    WHERE id_usuario = '$id_usuario'";

    mysqli_query($conexion, $sqlPass);

    /* =========================
       NOTIFICACIÓN
    ========================= */

    $mensaje =
    "Se cambió la contraseña del administrador";

    mysqli_query($conexion,"
    INSERT INTO notificaciones
    (mensaje,tipo,fecha)

    VALUES

    ('$mensaje','seguridad',NOW())
    ");

    header("Location: configuracion.php");
    exit();
}

/* =========================
   CONSULTAR VENDEDORES
========================= */

$vendedores = mysqli_query(
    $conexion,
    "SELECT * FROM empleado
    WHERE rol='vendedor'"
);

/* =========================
   CONSULTAR NOTIFICACIONES
========================= */

$notificaciones = mysqli_query(
    $conexion,
    "SELECT * FROM notificaciones
    ORDER BY id_notificacion DESC"
);

?>