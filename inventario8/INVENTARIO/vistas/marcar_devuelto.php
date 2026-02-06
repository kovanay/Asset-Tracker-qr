<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "main.php";
$conexion = conexion();

function enviarRespuestaJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_prestamo = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id_prestamo === false || $id_prestamo <= 0) {
        enviarRespuestaJSON(["success" => false, "error" => "ID de préstamo inválido."]);
    }

    try {
        $queryCheck = $conexion->prepare("SELECT Id_pre FROM prestamos WHERE Id_pre = ? AND devuelto = 0");
        $queryCheck->execute([$id_prestamo]);

        if ($queryCheck->rowCount() > 0) {
            $queryUpdate = $conexion->prepare("UPDATE prestamos SET devuelto = 1 WHERE Id_pre = ?");
            if ($queryUpdate->execute([$id_prestamo])) {
                $prestamo = $conexion->query("SELECT herramienta_pre FROM prestamos WHERE Id_pre = $id_prestamo")->fetch(PDO::FETCH_ASSOC);
                $productos = json_decode($prestamo['herramienta_pre'], true);

                if (is_array($productos)) {
                    foreach ($productos as $producto) {
                        if (is_array($producto) && isset($producto['nombre']) && isset($producto['cantidad'])) {
                            $nombre_producto = $producto['nombre'];
                            $cantidad_prestada = $producto['cantidad'];

                            $queryTipo = $conexion->prepare("SELECT tipo FROM producto WHERE producto_nombre = ?");
                            $queryTipo->execute([$nombre_producto]);
                            $tipo_articulo = $queryTipo->fetchColumn();

                            if ($tipo_articulo === 'no_consumible') {
                                $queryStock = $conexion->prepare("UPDATE producto SET producto_stock = producto_stock + ? WHERE producto_nombre = ?");
                                $queryStock->execute([$cantidad_prestada, $nombre_producto]);
                            }
                        }
                    }
                }
                enviarRespuestaJSON(["success" => true]);
            } else {
                enviarRespuestaJSON(["success" => false, "error" => "No se pudo marcar como devuelto."]);
            }
        } else {
            enviarRespuestaJSON(["success" => false, "error" => "Préstamo ya devuelto o no existe."]);
        }
    } catch (PDOException $e) {
        enviarRespuestaJSON(["success" => false, "error" => "Error en la BD: " . $e->getMessage()]);
    }
} else {
    enviarRespuestaJSON(["success" => false, "error" => "Solicitud inválida."]);
}
?>