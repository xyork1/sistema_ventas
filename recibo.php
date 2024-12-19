<?php
require_once 'conexionDB.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div style='color: red;'>No se encontró el recibo solicitado.</div>");
}

$venta_id = $_GET['id'];

$query = "SELECT ventas.*, servicios.nombre AS servicio_nombre 
          FROM ventas 
          JOIN servicios ON ventas.servicio_id = servicios.id
          WHERE ventas.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $venta_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $venta = $result->fetch_assoc();
} else {
    die("<div style='color: red;'>No se encontró la información del recibo.</div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .details {
            text-align: left;
            margin: 10px 0;
        }
        .button {
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }

        /* Ocultar botones y enlaces durante la impresión */
        @media print {
            .button,        /* Ocultar botones */
            a,              /* Ocultar enlaces */
            .no-print {     /* Ocultar contenido con esta clase */
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">Recibo de Venta</div>
    <div class="details">
        <p><strong>Código de Venta:</strong> <?php echo str_pad($venta['id'], 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($venta['vendedor']); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente']); ?></p>
        <p><strong>Servicio:</strong> <?php echo htmlspecialchars($venta['servicio_nombre']); ?></p>
        <p><strong>Duración:</strong> <?php echo htmlspecialchars($venta['duracion']); ?> horas</p>
        <p><strong>Precio Unitario:</strong> $<?php echo number_format($venta['precio'], 2); ?></p>
        <p><strong>Total:</strong> $<?php echo number_format($venta['total'], 2); ?></p>
        <p><strong>Fecha de Venta:</strong> <?php echo htmlspecialchars($venta['fecha_venta']); ?></p>
    </div>
    <button class="button no-print" onclick="window.print()">Imprimir Recibo</button>
    <br><br>
    <a href="index.php?rol=vendedor" class="button no-print">Volver al Inicio</a>
</div>
</body>
</html>
