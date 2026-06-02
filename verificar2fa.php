<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();

include("comunes/loginfunciones.php");

require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

// Si no viene de un login previo válido, mandamos al login
if (!isset($_SESSION['pre_auth_id']) || 
    !isset($_SESSION['pre_auth_secret'])) {
    redireccionar("login.php");
}

$error  = '';
$secret = $_SESSION['pre_auth_secret'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = trim($_POST['codigo_2fa'] ?? '');

    // Validar que el código sea numérico y de 6 dígitos
    if (empty($codigo) || !preg_match('/^\d{6}$/', $codigo)) {
        $error = "El código debe ser de 6 dígitos numéricos.";

    } else {

        $g = new GoogleAuthenticator();

        if ($g->checkCode($secret, $codigo)) {

            // Código correcto — crear sesión completa
            $_SESSION['autenticado']       = "SI";
            $_SESSION['id']                = $_SESSION['pre_auth_id'];
            $_SESSION['Usuario']           = $_SESSION['pre_auth_usuario'];
            $_SESSION['Correo']            = $_SESSION['pre_auth_correo'];

            // Limpiar variables temporales de pre-autenticación
            unset($_SESSION['pre_auth_id']);
            unset($_SESSION['pre_auth_usuario']);
            unset($_SESSION['pre_auth_correo']);
            unset($_SESSION['pre_auth_secret']);

            // Redirigir al dashboard
            redireccionar("dashboard.php");

        } else {
            $error = "Código incorrecto o expirado. Intenta de nuevo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación 2FA</title>
    <link rel="stylesheet" href="Estilos/estilos.css">
</head>
<body>
<div class="container">

    <div class="icono"></div>
    <h1>Verificación en dos pasos</h1>
    <p class="subtitulo">
        Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['pre_auth_usuario'] ?? ''); ?></strong>
    </p>

    <div class="tip">
        Abre <strong>Google Authenticator</strong> en tu celular 
        e ingresa el código de 6 dígitos que aparece para 
        <strong>LabLoginUTP</strong>.
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-msg"> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="verificar2fa.php">

        <fieldset>
            <legend>Código de verificación:</legend>
            <input type="text" name="codigo_2fa" id="codigo_2fa"
                   placeholder="000000" maxlength="6" 
                   autocomplete="off" autofocus>
        </fieldset>

        <button type="submit" class="btn-submit"> Verificar Código</button>

    </form>

    <div class="link-volver">
        <a href="login.php">← Volver al login</a>
    </div>

</div>
</body>
</html>