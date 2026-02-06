    <?php
    require_once "main.php";
    require('fpdf/fpdf.php'); // Incluir FPDF

    date_default_timezone_set("America/Hermosillo"); // Establecer zona horaria

    $conexion = conexion();
    $empleado_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Invitado";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tipo = $_POST['tipo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $matricula = $_POST['matricula'] ?? '';
        $carrera = $_POST['carrera'] ?? '';
        $area = $_POST['area'] ?? '';   

        // Formatear el nombre del solicitante
        $solicitante = $nombre;
        if ($tipo === "Estudiante") {
            $solicitante .= " (" . $carrera . ")";
        } elseif ($tipo === "Colaborador UTN") {
            $solicitante .= " (" . $area . ")";
        }

        // Obtener carrito de herramientas desde localStorage (simulado mediante cookies)
        if (isset($_COOKIE['carrito'])) {
            $herramientas = json_decode($_COOKIE['carrito'], true);
            $cantidad_total = array_sum(array_column($herramientas, 'cantidad'));
            $herramientas_serializadas = json_encode($herramientas);
        } else {
            $cantidad_total = 0;
            $herramientas_serializadas = "[]";
        }

        // Capturar fecha en formato DATETIME compatible con MySQL
        $fecha_prestamo = date("Y-m-d H:i:s");

        // Insertar en la base de datos
        $sql = "INSERT INTO prestamos (solicitante_pre, cantidad_pre, fechaprestamo_pre, tipo_pre, herramienta_pre)
                VALUES (:solicitante, :cantidad, :fecha, :tipo, :herramientas)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':solicitante', $solicitante);
        $stmt->bindParam(':cantidad', $cantidad_total, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha_prestamo);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':herramientas', $herramientas_serializadas);

        if ($stmt->execute()) {

            // Actualizar el stock de cada producto prestado
            if (isset($_COOKIE['carrito'])) {
                $herramientas = json_decode($_COOKIE['carrito'], true);
                if (is_array($herramientas)) {
                    foreach ($herramientas as $item) {
                        $sql_update = "UPDATE producto SET producto_stock = producto_stock - :cantidad WHERE producto_id = :id";
                        $stmt_update = $conexion->prepare($sql_update);
                        $stmt_update->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                        $stmt_update->bindParam(':id', $item['id'], PDO::PARAM_INT);
                        $stmt_update->execute();
                    }
                }
            }

            // Generar PDF con Mejor Diseño
            class PDF extends FPDF {
                public $proveedorNombre; // Nueva propiedad
    
                function setProveedorNombre($nombre) { // Nuevo método
                    $this->proveedorNombre = $nombre;
                }
    
                function Header() {
                    $this->Image('img/utn.png', 10, 6, 30); // (ruta, x, y, ancho)
                    $this->SetFont('Arial', 'B', 14);
                    $this->Cell(190, 10, 'COMPROBANTE DE PRESTAMO', 0, 1, 'C');
                    $this->Ln(10);
                }
    
                function Footer() {
                    $this->SetY(-15);
                    $this->SetFont('Arial', 'I', 8);
                    $this->Cell(0, 10, 'Documento generado automáticamente - ' . date("d/m/Y"), 0, 0, 'C');
                }
            }

            $pdf = new PDF();
            $pdf->setProveedorNombre($empleado_nombre);
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);

            // Datos del préstamo
            $pdf->Cell(50, 10, 'Solicitante:', 0, 0);
            $pdf->Cell(100, 10, utf8_decode($solicitante), 0, 1);
            $pdf->Cell(50, 10, 'Tipo:', 0, 0);
            $pdf->Cell(100, 10, $tipo, 0, 1);
            $pdf->Cell(50, 10, 'Matricula/ID:', 0, 0);
            $pdf->Cell(100, 10, $matricula, 0, 1);
            $pdf->Cell(50, 10, 'Fecha:', 0, 0);
            $pdf->Cell(100, 10, date("d/m/Y H:i:s", strtotime($fecha_prestamo)), 0, 1);
            $pdf->Cell(50, 10, 'Proveedor:', 0, 0); // Nueva celda para el proveedor
            $pdf->Cell(100, 10, utf8_decode($pdf->proveedorNombre), 0, 1); // Mostrar el nombre del proveedor
            $pdf->Ln(10);

            // Encabezado de la tabla
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(130, 10, 'Herramienta', 1, 0, 'C');
            $pdf->Cell(30, 10, 'Cantidad', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 12);

            if (isset($herramientas) && is_array($herramientas)) {
                foreach ($herramientas as $h) {
                    $pdf->Cell(130, 10, utf8_decode($h['nombre']), 1, 0);
                    $pdf->Cell(30, 10, $h['cantidad'], 1, 1, 'C');
                }
            }
            $pdf->Ln(10);

            // Sección de firma
            $pdf->Cell(50, 10, 'Firma del Solicitante:', 0, 1);
            $pdf->Cell(100, 10, '_________________________', 0, 1);
            $pdf->Ln(10);

            if (!is_dir("pdfs")) {
                mkdir("pdfs", 0777, true);
            }

            $ruta_pdf = "pdfs/prestamo_" . time() . ".pdf";
            $pdf->Output($ruta_pdf, 'F');

            echo "<script>
                alert('Préstamo registrado exitosamente. Descargando PDF...');
                window.location.href='$ruta_pdf';
            </script>";
        } else {
            echo "<script>alert('Error al registrar el préstamo');</script>";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang=\"es\">
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Registro de Préstamo</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                margin: auto;
            }
            .hidden {
                display: none;
            }
            .button {
                background-color: #007bff;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                display: block;
                width: 100%;
                margin-top: 15px;
            }
            .button:hover {
                background-color: #0056b3;
            }
        </style>
         <script>
        function mostrarFormulario(tipo) {
            document.getElementById("formEstudiante").classList.add("hidden");
            document.getElementById("formColaborador").classList.add("hidden");
            
            if (tipo === 'Estudiante') {
                document.getElementById("formEstudiante").classList.remove("hidden");
                document.getElementById("tipoEstudiante").value = tipo;
            } else {
                document.getElementById("formColaborador").classList.remove("hidden");
                document.getElementById("tipoColaborador").value = tipo;
            }
        }
        function guardarCarrito() {
            let carrito = localStorage.getItem("carrito");
            document.cookie = "carrito=" + carrito + "; path=/";
        }
    </script>
    </head>
    <body>
        <div class="container">
            <h2>Registro de Préstamo</h2>
            <p>Seleccione su tipo de usuario:</p>
            <button class="button" onclick="mostrarFormulario('Estudiante')">Estudiante</button>
            <button class="button" onclick="mostrarFormulario('Colaborador UTN')">Colaborador UTN</button>
            
            <form id="formEstudiante" class="hidden" action="index.php?vista=prestamos" method="POST" onsubmit="guardarCarrito()">
                <input type="hidden" name="tipo" id="tipoEstudiante">
                <label for="carrera">Carrera:</label>
                <select name="carrera" required>
                    <option value="">Seleccione su carrera</option>
                    <option value="Tecnologías de la Información">Tecnologías de la Información</option>
                    <option value="Mecatrónica">Mecatrónica</option>
                    <option value="Procesos Industriales">Procesos Industriales</option>
                    <option value="Operaciones Comerciales">Operaciones Comerciales</option>
                    <option value="Energías Renovables">Energías Renovables</option>
                    <option value="Desarrollo de Negocios">Desarrollo de Negocios</option>
                    <option value="Manufactura Aeronáutica">Manufactura Aeronáutica</option>
                    <option value="Mantenimiento Industrial">Mantenimiento Industrial</option>
                    <option value="Ing. en Mecatrónica">Ing. en Mecatrónica</option>
                    <option value="Ing. en Energías Renovables">Ing. en Energías Renovables</option>
                    <option value="Ing. Mantenimiento Industrial">Ing. Mantenimiento Industrial</option>
                    <option value="Ing. En Desarrollo y Gestión SW">Ing. En Desarrollo y Gestión SW</option>
                    <option value="Ing. Sistemas Productivos">Ing. Sistemas Productivos</option>
                    <option value="Ing. Logística Internacional">Ing. Logística Internacional</option>
                    <option value="Ing. Manufactura Aeronáutica">Ing. Manufactura Aeronáutica</option>
                    <option value="Ing. Innovación de Negocios y MKT">Ing. Innovación de Negocios y MKT</option>
                </select>
                <br><br>
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre" required>
                <br><br>
                <label for="matricula">Matrícula:</label>
                <input type="text" name="matricula" required>
                <br><br>
                <button type="submit" class="button">Listo</button>
            </form>
            
            <form id="formColaborador" class="hidden" action="index.php?vista=prestamos" method="POST" onsubmit="guardarCarrito()">
                <input type="hidden" name="tipo" id="tipoColaborador">
                <label for="area">Área:</label>
                <input type="text" name="area" required>
                <br><br>
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre" required>
                <br><br>

                <label for="matricula">Matrícula/Numero celular:</label>
                <input type="text" name="matricula" required>
                <br><br>
                <button type="submit" class="button">Listo</button>
            </form>
        </div>
    </body>
    </html>
