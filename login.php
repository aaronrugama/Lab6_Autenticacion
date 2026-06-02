<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();

include("clases/mysql.inc.php");
include("clases/SanitizarEntrada.php");
include("comunes/loginfunciones.php");

$error   = '';
$clasePDO = null;
$pdo      = null;

try {

    $clasePDO = new mod_db();
    $pdo      = $clasePDO->getConexion();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $ip      = $_SERVER['REMOTE_ADDR'];
        $usuario = SanitizarEntrada::limpiarEspacios($_POST['usuario'] ?? '');
        $clave   = SanitizarEntrada::limpiarPassword($_POST['clave'] ?? '');

        // Validar que no vengan vacíos
        if (empty($usuario) || empty($clave)) {
            $error = "Por favor completa todos los campos.";

        } else {

            // Buscar el usuario por correo
            $sql  = "SELECT * FROM usuarios WHERE Correo = :usuario LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":usuario", $usuario, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($clave, $user['HashMagic'])) {

                // Credenciales correctas — registrar intento exitoso
                registrarIntentoLogin($pdo, $usuario, $ip, true);

                // Guardar datos temporales en sesión para el paso 2FA
                $_SESSION['pre_auth_id']      = $user['id'];
                $_SESSION['pre_auth_usuario']  = $user['Usuario'];
                $_SESSION['pre_auth_correo']   = $user['Correo'];
                $_SESSION['pre_auth_secret']   = $user['secret_2fa'];

                // Redirigir a verificación 2FA
                redireccionar("verificar2fa.php");

            } else {

                // Credenciales incorrectas — registrar intento fallido
                registrarIntentoLogin($pdo, $usuario, $ip, false);
                $error = "Usuario o contraseña incorrectos.";
            }
        }
    }

} catch (Exception $e) {
    $error = "Ha ocurrido un error. Intente más tarde.";
} finally {
    $pdo      = null;
    $clasePDO = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="Estilos/estilos.css">
</head>
<body>
<div class="login-container">
    <h1>Iniciar Sesión</h1>

    <?php if (!empty($error)): ?>
        <div class="error-msg"> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <fieldset>
            <legend>Correo electrónico:</legend>
            <input type="email" name="usuario" id="usuario" 
                   placeholder="correo@ejemplo.com" required>
        </fieldset>

        <fieldset>
            <legend>Contraseña:</legend>
            <input type="password" name="clave" id="clave" 
                   placeholder="Tu contraseña" required>
        </fieldset>

        <button type="submit" class="btn-submit">Entrar</button>

    </form>

    <div class="link-registro">
        ¿No tienes cuenta? <a href="registrese_form.php">Regístrate aquí</a>
    </div>
</div>
</body>
</html>