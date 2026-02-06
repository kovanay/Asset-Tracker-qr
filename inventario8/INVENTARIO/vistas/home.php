<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlace a tu CSS -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url('./img/FONDO_1.jpg');
            /* Ruta de tu imagen */
            background-size: cover;
            /* Escala la imagen para cubrir toda la pantalla */
            background-position: center;
            /* Centra la imagen */
            background-repeat: no-repeat;
            /* No repite la imagen */
        }

        .container {
            background-color: rgb(255, 255, 255);
            /* Fondo blanco semitransparente */
            padding: 20px;
            border-radius: 10px;
            margin: 50px auto;
            max-width: 800px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .title {
            font-size: 2rem;
            color: #333;
        }

        .subtitle {
            font-size: 1.5rem;
            color: #666;
        }
    </style>
    <style>
        .container-abc123 {
            text-align: center;
            margin-bottom: 50px;
        }

        .title-xyz456 {
            font-size: 2.5rem;
            color: #333;
        }

        .subtitle-pqr789 {
            font-size: 1.8rem;
            color: #666;
        }

        .button-group-lmn999 {
            display: flex;
            justify-content: center;
            gap: 20px;
            /* Espaciado entre los botones */
        }

        .btn-primary-jkl001 {
            display: inline-block;
            padding: 12px 25px;
            /* Tamaño de los botones */
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 8px;
            /* Bordes ligeramente redondeados */
            transition: all 0.3s ease;
            background-color: rgba(27, 183, 7, 0.8);
            /* Color azul con transparencia */
            color: #fff;
            border: 1px solid rgba(253, 254, 254, 0.6);
            /* Borde con transparencia */
        }

        .btn-primary-jkl001:hover {
            background-color: rgb(1, 114, 5);
            box-shadow: 0 6px 12px rgba(0, 91, 187, 0.4);
        }

        .btn-secondary-uvw777 {
            display: inline-block;
            padding: 12px 25px;
            /* Tamaño de los botones */
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 8px;
            /* Bordes ligeramente redondeados */
            transition: all 0.3s ease;
            background-color: rgba(0, 123, 255, 0.8);
            /* Color azul con transparencia */
            color: #fff;
            border: 1px solid rgba(0, 123, 255, 0.6);
            /* Borde con transparencia */
        }

        .btn-secondary-uvw777:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 12px rgba(0, 91, 187, 0.4);
        }
    </style>


</head>

<body>
    <div class="container is-fluid">
        <h1 class="title">INICIO</h1>
        <h2 class="subtitle">¡Bienvenido <?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?>!</h2>
    </div>

    <div class="button-group-lmn999">


        <a href="index.php?vista=product_list" class="btn-secondary-uvw777">
            ver productos
        </a>

        <a href="index.php?vista=prestamoscarrito" class="btn-secondary-uvw777">
            Prestamos
        </a>
    </div>


</body>

</html>