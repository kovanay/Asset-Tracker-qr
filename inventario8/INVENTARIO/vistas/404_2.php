<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Error 404 | Página no encontrada</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, sans-serif;
    }

    body {
      min-height: 100vh;
      background: #f2fbff;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Figuras decorativas */
    .shape-left {
      position: absolute;
      top: 0;
      left: 0;
      width: 220px;
      height: 100%;
      background: linear-gradient(180deg, #8fd3f4, #84fab0);
      clip-path: polygon(0 0, 100% 0, 60% 100%, 0 100%);
      opacity: 0.35;
    }

    .shape-right {
      position: absolute;
      bottom: 0;
      right: 0;
      width: 260px;
      height: 100%;
      background: linear-gradient(180deg, #6fb1fc, #4facfe);
      clip-path: polygon(40% 0, 100% 0, 100% 100%, 0 100%);
      opacity: 0.25;
    }

    .container {
      background: #ffffff;
      padding: 50px 60px;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      text-align: center;
      z-index: 1;
      max-width: 420px;
      width: 100%;
    }

    .container h1 {
      font-size: 96px;
      color: #2f80ed;
      margin-bottom: 10px;
    }

    .container h2 {
      font-size: 22px;
      color: #333;
      margin-bottom: 12px;
    }

    .container p {
      color: #666;
      font-size: 15px;
      margin-bottom: 30px;
    }

    .btn-home {
      display: inline-block;
      padding: 14px 30px;
      background: linear-gradient(135deg, #2f80ed, #56ccf2);
      color: #fff;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 6px 16px rgba(47, 128, 237, 0.35);
    }

    .btn-home:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 22px rgba(47, 128, 237, 0.45);
    }

    @media (max-width: 480px) {
      .container {
        padding: 40px 25px;
      }

      .container h1 {
        font-size: 72px;
      }
    }
  </style>
</head>

<body>

  <div class="shape-left"></div>
  <div class="shape-right"></div>

  <div class="container">
    <h1>404</h1>
    <h2>Página no encontrada</h2>
    <p>La página que intentas visitar no existe o fue movida.</p>
    <a href="index.php" class="btn-home">Regresar al inicio</a>
  </div>

</body>

</html>