<?php
session_start();
if ($_SESSION['rol'] !== 'vendedor') {
    header('Location: index.php');
    exit();
}

require_once 'conexionDB.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Vendedor</title>
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
        h1, h2 {
            text-align: center;
        }
        form {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
        p {
            font-size: 1.2em;
            margin-top: 10px;
        }
        a {
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Interfaz del Vendedor</h1>
    <form action="procesar_venta.php" method="POST" enctype="multipart/form-data">
    <label for="vendedor">Nombre del Vendedor:</label>
    <input type="text" id="vendedor" name="vendedor" required>

    <label for="cliente">Nombre del Cliente:</label>
    <input type="text" id="cliente" name="cliente" required>

    <label for="servicio">Seleccione el Servicio:</label>
    <select id="servicio" name="servicio" required onchange="calcularTotal()">
        <option value="" data-precio="0">Seleccione un servicio</option>
        <?php
        // Consulta a la base de datos para cargar los servicios
        $query = "SELECT * FROM servicios";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}' data-precio='{$row['precio']}'>
                        {$row['nombre']} - \${$row['precio']} ({$row['detalles']})
                      </option>";
            }
        }
        ?>
    </select>

    <label for="duracion">Duración (horas):</label>
    <input type="number" step="0.1" id="duracion" name="duracion" required oninput="calcularTotal()">

    <label for="comprobante">Subir Comprobante (imagen):</label>
    <input type="file" id="comprobante" name="comprobante" accept="image/*" required>

    <p><strong>Total General:</strong> $<span id="total">0.00</span></p>

    <button type="submit" class="button">Registrar Venta</button>
</form>

<script>
function calcularTotal() {
    const servicio = document.getElementById('servicio');
    const duracion = parseFloat(document.getElementById('duracion').value) || 0;
    const precio = parseFloat(servicio.options[servicio.selectedIndex].getAttribute('data-precio')) || 0;
    const total = precio * duracion;

    document.getElementById('total').textContent = total.toFixed(2);
}
</script>


    <div style="text-align: center; margin-top: 20px;">
        <button class="button"><a href="logout.php">Cerrar Sesión</a></button>
    </div>
</div>
<script>
    function calcularTotal() {
        const servicioSelect = document.getElementById('servicio');
        const duracionInput = document.getElementById('duracion');
        const totalSpan = document.getElementById('total');

        const precio = parseFloat(servicioSelect.options[servicioSelect.selectedIndex].dataset.precio || 0);
        const duracion = parseFloat(duracionInput.value || 0);

        const total = (precio * duracion).toFixed(2);
        totalSpan.textContent = total;
    }
</script>
</body>
</html>
