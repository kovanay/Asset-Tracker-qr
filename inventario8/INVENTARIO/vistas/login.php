<?php
// Iniciar la sesión si aún no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Si se envió el formulario de inicio de sesión
if (isset($_POST['login_usuario']) && isset($_POST['login_clave'])) {
  require_once "./php/main.php";
  require_once "./php/iniciar_sesion.php";
}
?>

<style>
  :root {
    --text-color: #333;
    --label-color: #555;
    --button-bg-color: #4193ce;
    --button-text-color: #fff;
    --button-hover-bg-color: hsl(206, 85.30%, 42.70%);
  }

  .main-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('./img/IMG_MAN5.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  .main-container .box {
    width: 50%;
    max-width: 400px;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.35);
    box-shadow: 0 4px 10px rgb(56, 4, 4);
    border-radius: 8px;
  }

  .main-container .title {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--text-color);
  }

  .main-container .field .label {
    font-weight: bold;
    color: var(--label-color);
  }

  .main-container .input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    width: 100%;
  }

  .main-container .button {
    background-color: var(--button-bg-color);
    color: var(--button-text-color);
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 20px;
    transition: background-color 0.3s;
    width: 100%;
  }

  .main-container .button:hover {
    background-color: var(--button-hover-bg-color);
  }
</style>

<div class="main-container">
  <form class="box login" action="" method="POST" autocomplete="off">
    <h5 class="title is-5 has-text-centered is-uppercase">¡Bienvenido!</h5>

    <div class="field">
      <label class="label">Usuario</label>
      <div class="control">
        <input class="input" type="text" name="login_usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required>
      </div>
    </div>

    <div class="field">
      <label class="label">Clave</label>
      <div class="control">
        <input class="input" type="password" name="login_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required>
      </div>
    </div>

    <p class="has-text-centered mb-4 mt-3">
      <button type="submit" class="button is-info is-rounded">Iniciar sesión</button>
    </p>

    <?php
    // Si hay un error en la autenticación, mostrar mensaje
    if (isset($_SESSION['login_error'])) {
      echo "<p style='color: red; text-align: center;'>" . $_SESSION['login_error'] . "</p>";
      unset($_SESSION['login_error']); // Eliminar mensaje después de mostrarlo
    }
    ?>
  </form>
</div>