<?php
require_once "main.php";
date_default_timezone_set("America/Hermosillo"); // Establecer zona horaria
$conexion = conexion();

// Obtener préstamos pendientes (devuelto = 0)
$prestamos = $conexion->query("SELECT * FROM prestamos WHERE devuelto = 0")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos Pendientes</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            padding: 20px; 
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .boton-devuelto {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }
        .boton-devuelto:hover {
            background: #c82333;
        }
        .devuelto {
            background: #ffc107 !important;
        }
        .tiempo-extendido {
            font-weight: bold;
            color: black;
            display: block;
            margin-top: 5px;
        }
        .detalle {
            display: none;
            background-color: #f9f9f9;
        }
    </style>
    <script>
        function toggleDetalle(id) {
            let filaDetalle = document.getElementById("detalle-" + id);
            filaDetalle.style.display = (filaDetalle.style.display === "none") ? "table-row" : "none";
        }

        function marcarDevuelto(id, event) {
        event.stopPropagation(); // Evita que se cierre el detalle al hacer clic en el botón

        if (confirm("¿Estás seguro de que deseas marcar este préstamo como devuelto?")) {
            fetch('vistas/marcar_devuelto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        }) 
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let fila = document.getElementById('fila-' + id);
                let detalle = document.getElementById('detalle-' + id);
                if (fila) fila.remove(); // Eliminar la fila principal
                if (detalle) detalle.remove(); // Eliminar la fila de detalles
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            alert("Hubo un error al procesar la solicitud.");
        });
        }

        }

        function iniciarContador(id, fechaPrestamo) {
            let tiempoLimite = new Date(fechaPrestamo).getTime() + 60 * 60 * 1000;
            let intervalo = setInterval(() => {
                let tiempoRestante = tiempoLimite - new Date().getTime();
                let elemento = document.getElementById('fila-' + id);
                let botonAccion = document.getElementById('accion-' + id);
                let boton = botonAccion.querySelector('button'); // Obtener el botón

                if (tiempoRestante <= 0) {
                    clearInterval(intervalo);
                    elemento.classList.add('devuelto');
                    
                    // Crear el mensaje y agregarlo junto al botón
                    let span = document.createElement('span');
                    span.className = 'tiempo-extendido';
                    span.textContent = 'Tiempo extendido';
                    botonAccion.appendChild(span);
                }
            }, 1000);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Préstamos Pendientes</h2>
        <table>
            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>Fecha Préstamo</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prestamos as $p) { 
                    // Decodificar herramientas (aseguramos que sea un array)
                    $productos = json_decode($p['herramienta_pre'], true);
                    if (!is_array($productos)) {
                        $productos = []; // Si falla la conversión, asignamos un array vacío
                    }
                ?>
                <!-- Fila principal -->
                <tr id="fila-<?= $p['Id_pre'] ?>" onclick="toggleDetalle(<?= $p['Id_pre'] ?>)">
                    <td><?= htmlspecialchars($p['solicitante_pre']) ?></td>
                    <td><?= htmlspecialchars($p['fechaprestamo_pre']) ?></td>
                    <td><?= htmlspecialchars($p['tipo_pre']) ?></td>
                    <td><?= htmlspecialchars($p['cantidad_pre']) ?></td>
                    <td id="accion-<?= $p['Id_pre'] ?>">
                        <button class="boton-devuelto" onclick="marcarDevuelto(<?= $p['Id_pre'] ?>, event)">Devuelto</button>
                    </td>
                </tr>
                <!-- Fila de detalles (oculta por defecto) -->
                <tr id="detalle-<?= $p['Id_pre'] ?>" class="detalle">
                    <td colspan="5">
                        <strong>Productos prestados:</strong>
                        <ul>
                            <?php 
                            foreach ($productos as $producto) { 
                                // Verificar si el producto es un array y tiene las claves 'nombre' y 'cantidad'
                                if (is_array($producto) && isset($producto['nombre']) && isset($producto['cantidad'])) {
                                    echo '<li>' . htmlspecialchars($producto['nombre']) . ' - Cantidad: ' . htmlspecialchars($producto['cantidad']) . '</li>';
                                } else {
                                    echo '<li>Información del producto no disponible.</li>';
                                }
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
                <script>iniciarContador(<?= $p['Id_pre'] ?>, '<?= $p['fechaprestamo_pre'] ?>');</script>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
