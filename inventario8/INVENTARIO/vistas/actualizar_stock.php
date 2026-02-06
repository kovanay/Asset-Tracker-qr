<?php
require_once "main.php";
$conexion = conexion();

// Obtener lista de productos con stock actualizado
$productos = $conexion->query("SELECT producto_id, producto_nombre, producto_stock, producto_foto FROM producto WHERE producto_stock > 0")->fetchAll();

// Obtener el usuario logueado
$empleado_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Invitado";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamo de Herramientas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .producto {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            cursor: pointer;
        }

        .producto:hover {
            background: #eef;
        }

        .producto img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .carrito-container {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .boton-siguiente {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        .boton-siguiente:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .imagen-seleccionada img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
    <script>
        let carrito = [];

        function agregarAlCarrito(id, nombre, stock) {
            // Verificar si el producto ya está en el carrito
            const productoEnCarrito = carrito.find(item => item.id === id);
            const cantidadEnCarrito = productoEnCarrito ? productoEnCarrito.cantidad : 0;
            const stockRestante = stock - cantidadEnCarrito;

            if (stockRestante <= 0) {
                alert('No hay más stock disponible para', "${nombre}");
                return;
            }

            let cantidad = prompt('Ingrese cantidad para ' + nombre('Stock disponible:' + stockRestante));
            cantidad = parseInt(cantidad);

            if (isNaN(cantidad) || cantidad <= 0) {
                alert("Cantidad inválida.");
                return;
            }

            if (cantidad > stockRestante) {
                alert('Solo puedes agregar hasta ' + stockRestante + ' unidades de' + "${nombre}");
                return;
            }

            // Si el producto ya está en el carrito, actualiza la cantidad
            if (productoEnCarrito) {
                productoEnCarrito.cantidad += cantidad;
            } else {
                carrito.push({
                    id,
                    nombre,
                    cantidad
                });
            }

            actualizarCarrito();
            verificarCarrito();
        }

        function actualizarCarrito() {
            const lista = document.getElementById("listaCarrito");
            lista.innerHTML = "";
            carrito.forEach((item, index) => {
                lista.innerHTML += `<li>${item.nombre} (x${item.cantidad}) 
                <button onclick='removerDelCarrito(${index})'>X</button></li>`;
            });
        }

        function removerDelCarrito(index) {
            carrito.splice(index, 1);
            actualizarCarrito();
            verificarCarrito();
        }

        function verificarCarrito() {
            document.getElementById("btnSiguiente").disabled = carrito.length === 0;
        }

        function guardarYContinuar() {
            if (carrito.length === 0) return;

            fetch('actualizar_stock.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(carrito)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Préstamo registrado exitosamente.');
                        localStorage.removeItem("carrito");
                        window.location.reload();
                    } else {
                        alert('Error al procesar el préstamo: ' + data.error);
                    }
                })
                .catch(error => alert('Error de conexión: ' + error));
        }

        function mostrarImagen(src) {
            document.getElementById("imagenSeleccionada").src = src;
        }
    </script>
</head>

<body>
    <div class="container">
        <p>Empleado: <?= htmlspecialchars($empleado_nombre) ?></p>

        <h2>Seleccionar Productos</h2>

        <?php foreach ($productos as $producto) { ?>
            <div class="producto"
                onclick="agregarAlCarrito(<?= $producto['producto_id'] ?>, '<?= $producto['producto_nombre'] ?>', <?= $producto['producto_stock'] ?>)"
                onmouseover="mostrarImagen('./img/producto/<?= $producto['producto_foto'] ?>')">
                <span><?= htmlspecialchars($producto['producto_nombre']) ?> (Stock: <?= $producto['producto_stock'] ?>)</span>
                <img src="./img/producto/<?= $producto['producto_foto'] ?>">
            </div>
        <?php } ?>

        <div class="carrito-container">
            <h2>Carrito de Préstamos</h2>
            <ul id="listaCarrito"></ul>
        </div>

        <div class="imagen-seleccionada">
            <h3>Imagen del Producto</h3>
            <img id="imagenSeleccionada" src="./img/producto.png">
        </div>

        <button id="btnSiguiente" class="boton-siguiente" onclick="guardarYContinuar()" disabled>
            Siguiente
        </button>
    </div>
</body>

</html>