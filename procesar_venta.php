<?php
require_once 'conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendedor = $_POST['vendedor'];
    $cliente = $_POST['cliente'];
    $servicio_id = $_POST['servicio'];
    $duracion = $_POST['duracion'];

    if ($duracion <= 0) {
        die("<div style='color: red;'>La duración debe ser mayor a 0.</div>");
    }

    $query_servicio = "SELECT nombre, precio FROM servicios WHERE id = ?";
    $stmt_servicio = $conn->prepare($query_servicio);
    $stmt_servicio->bind_param("i", $servicio_id);
    $stmt_servicio->execute();
    $result_servicio = $stmt_servicio->get_result();

    if ($result_servicio->num_rows > 0) {
        $servicio = $result_servicio->fetch_assoc();
        $precio_unitario = $servicio['precio'];
        $total = $duracion * $precio_unitario;

        
        $rutaFinalComprobante = null;

        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $directorioDestino = __DIR__ . '/comprobantes/'; // Ruta de la carpeta "comprobantes"

          
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
            }

            $nombreArchivo = basename($_FILES['comprobante']['name']);
            $rutaTemp = $_FILES['comprobante']['tmp_name'];
            $rutaFinalComprobante = $directorioDestino . uniqid() . '_' . $nombreArchivo;

            $tipoArchivo = $_FILES['comprobante']['type'];
            if (!in_array($tipoArchivo, ['image/jpeg', 'image/png', 'image/gif'])) {
            die("<div style='color: red;'>Formato de archivo no permitido. Solo JPG, PNG o GIF.</div>");
            }

            if (!move_uploaded_file($rutaTemp, $rutaFinalComprobante)) {
                die("<div style='color: red;'>Error al guardar el comprobante.</div>");
            }

            chmod($rutaFinalComprobante, 0644); // Permisos del archivo
        } else {
            die("<div style='color: red;'>Debe subir un comprobante válido.</div>");
        }

       $query_venta = "INSERT INTO ventas (vendedor, cliente, servicio_id, duracion, precio, total, comprobante) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_venta = $conn->prepare($query_venta);
        $stmt_venta->bind_param("ssiddss", $vendedor, $cliente, $servicio_id, $duracion, $precio_unitario, $total, $rutaFinalComprobante);

        if ($stmt_venta->execute()) {
            $venta_id = $stmt_venta->insert_id;

            header("Location: recibo.php?id=$venta_id");
            exit();
        } else {
            echo "<div style='color: red;'>Error al registrar la venta: " . $stmt_venta->error . "</div>";
        }
    } else {
        echo "<div style='color: red;'>Servicio no encontrado.</div>";
    }

    $stmt_servicio->close();
    $stmt_venta->close();
    $conn->close();
} else {
    echo "<div style='color: red;'>Método no permitido.</div>";
}
?>
