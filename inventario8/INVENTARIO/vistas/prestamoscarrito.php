<?php
require_once "main.php";

// Conectar a la base de datos
$conexion = conexion();

// Obtener lista de usuarios
$usuarios = $conexion->query("SELECT usuario_id, usuario_nombre, usuario_apellido FROM usuario")->fetchAll();

// Obtener productos con stock disponible
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

        .carrito-container {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 100%;
        }

        .contenido {
            display: flex;
            gap: 20px;
        }

        .productos-lista {
            width: 60%;
            max-height: 400px; /* Ajusta este valor según necesites */
            overflow-y: auto;
        }

        .producto {
            display: flex;
            align-items: center;
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

        .imagen-carrito-container {
            width: 40%;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .imagen-seleccionada img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 5px;
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

        /* Botón "Ver préstamos en curso" */
        .boton-ver-prestamos {
            background-color: #ffc107; /* Amarillo */
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }

        .boton-ver-prestamos:hover {
            background-color: #e0a800;
        }
    </style>
    <script>
        let carrito = [];

        function agregarAlCarrito(id, nombre, stock) {
            let cantidad = prompt(`Ingrese cantidad para "${nombre}" (Stock: ${stock}):`);
            cantidad = parseInt(cantidad);
            if (cantidad > 0 && cantidad <= stock) {
                carrito.push({ id, nombre, cantidad });
                actualizarCarrito();
                verificarCarrito();
            } else {
                alert("Cantidad inválida.");
            }
        }

        function actualizarCarrito() {
            let lista = document.getElementById("listaCarrito");
            lista.innerHTML = "";
            carrito.forEach((item, index) => {
                lista.innerHTML += `<li><span class="math-inline">\{item\.nombre\} \(x</span>{item.cantidad}) <button onclick='removerDelCarrito(${index})'>X</button></li>`;
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
            localStorage.setItem("carrito", JSON.stringify(carrito));
            window.location.href = "index.php?vista=prestamos"; // Corrección aplicada
        }

        function mostrarImagen(src) {
            document.getElementById("imagenSeleccionada").src = src;
        }
    </script>
</head>
<body>
    <div class="container">
        <p class="empleado">Empleado: <?= htmlspecialchars($empleado_nombre) ?></p>

        <div class="contenido">
            <div class="productos-lista">
                <h2>Seleccionar Productos</h2>
                <?php foreach ($productos as $producto) { ?>
                    <div class="producto" onmouseover="mostrarImagen('./img/producto/<?= $producto['producto_foto'] ?>')"
                         onclick="agregarAlCarrito(<?= $producto['producto_id'] ?>, '<?= $producto['producto_nombre'] ?>', <?= $producto['producto_stock'] ?>)">
                        <span><?= $producto['producto_nombre'] ?> (Stock: <?= $producto['producto_stock'] ?>)</span>
                        <img src="./img/producto/<?= $producto['producto_foto'] ?>">
                    </div>
                <?php } ?>
            </div>

            <div class="imagen-carrito-container">
                <button class="boton-ver-prestamos" onclick="window.location.href='index.php?vista=pendientes'">
                    Ver préstamos en curso
                </button>

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