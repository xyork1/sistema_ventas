<?php
session_start();
if ($_SESSION['rol'] !== 'dueno') {
    header('Location: index.php');
    exit();
}

require_once 'conexionDB.php';

$fecha_consulta = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d'); // Fecha actual por defecto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header, .content {
            text-align: center;
        }
        .button {
            padding: 10px 20px;
            margin: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Resumen de Ventas</h1>
    </div>
    <div class="content">
        <h3>Ventas del día: <?php echo htmlspecialchars($fecha_consulta); ?></h3>

        <form action="vendedor.php" method="GET">
            <input type="hidden" name="rol" value="dueno">
            <label for="fecha">Seleccionar fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha_consulta); ?>" required>
            <button type="submit" class="button">Consultar</button>
        </form>

        <table>
            <tr>
                <th>ID Venta</th>
                <th>Vendedor</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Duración</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
            <?php
            $query = "SELECT ventas.*, servicios.nombre AS servicio_nombre 
                      FROM ventas 
                      JOIN servicios ON ventas.servicio_id = servicios.id
                      WHERE DATE(fecha_venta) = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $fecha_consulta);
            $stmt->execute();
            $result = $stmt->get_result();

            $total_general = 0; 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_general += $row['total']; 
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['vendedor']}</td>
                            <td>{$row['cliente']}</td>
                            <td>{$row['servicio_nombre']}</td>
                            <td>{$row['duracion']} horas</td>
                            <td>\${$row['total']}</td>
                            <td>{$row['fecha_venta']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No hay ventas registradas para esta fecha.</td></tr>";
            }
            ?>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total General:</strong></td>
                <td colspan="2"><strong>$<?php echo number_format($total_general, 2); ?></strong></td>
            </tr>
        </table>

        <h3>Resumen de Ventas por Vendedor</h3>
        <table>
            <tr>
                <th>Vendedor</th>
                <th>Total Vendido</th>
            </tr>
            <?php
            $query_resumen = "SELECT vendedor, SUM(total) AS total_vendido 
                              FROM ventas 
                              WHERE DATE(fecha_venta) = ?
                              GROUP BY vendedor";
            $stmt_resumen = $conn->prepare($query_resumen);
            $stmt_resumen->bind_param("s", $fecha_consulta);
            $stmt_resumen->execute();
            $result_resumen = $stmt_resumen->get_result();

            if ($result_resumen->num_rows > 0) {
                while ($row = $result_resumen->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['vendedor']}</td>
                            <td>\${$row['total_vendido']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No hay ventas registradas para esta fecha.</td></tr>";
            }
            ?>
        </table>

        <div style="margin-top: 30px;">
            <a href="logout.php" class="button">Cerrar Sesión</a>
        </div>
    </div>
</div>
</body>
</html>
