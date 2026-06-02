<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include("clases/mysql.inc.php");
include("clases/SanitizarEntrada.php");
include("clases/ClaseRegistrese.php");
include("comunes/loginfunciones.php");

require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$arrMensaje = array();

try {
    $ip = $_SERVER['REMOTE_ADDR'];

    $clasePDO = new mod_db();
    $pdo      = $clasePDO->getConexion();

    if (isset($_POST['Accion']) && $_POST['Accion'] === "Guardar") {

        $MyRegistro = new RegistroUsuario($_POST, $clasePDO, $arrMensaje);

        if (count($arrMensaje) === 0) {

            // Guardar usuario en la BD
            $MyRegistro->Guardar_RegistroUsuario();

            // Generar el secreto 2FA
            $g      = new GoogleAuthenticator();
            $secret = $g->generateSecret();

            // Guardar el secreto en la BD
            $MyRegistro->GuardarMySecreto($secret);

            // Generar URL del código QR en formato otpauth estándar
            $nombre_usuario    = $MyRegistro->getCorreo();
            $nombre_aplicacion = 'LabLoginUTP';

            // Formato otpauth:// que Google Authenticator reconoce correctamente
            $otpauth = 'otpauth://totp/' . urlencode($nombre_aplicacion . ':' . $nombre_usuario)
                    . '?secret=' . $secret
                    . '&issuer=' . urlencode($nombre_aplicacion)
                    . '&algorithm=SHA1'
                    . '&digits=6'
                    . '&period=30';

            $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);

            // Mostrar el QR al usuario
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Registro Exitoso - Escanea tu QR</title>
                <link rel="stylesheet" href="Estilos/estilos.css">
            </head>
            <body>
                <div class="qr-container">
                    <h2>Registro Exitoso</h2>
                    <p>Escanea este código QR con la app <strong>Google Authenticator</strong> 
                       para activar tu segundo factor de autenticación.</p>
                    <img src="<?php echo $qr_url; ?>" alt="Código QR para Google Authenticator">
                    <p class="qr-label">Abre Google Authenticator → Agregar cuenta → Escanear QR</p>
                    <a href="login.php" class="btn-login">Ir al Login</a>
                </div>
            </body>
            </html>
            <?php
            exit();

        } else {
            // Hay errores de validación
            foreach ($arrMensaje as $val) {
                echo "<p style='color:red;'> " . $val . "</p>";
            }
        }
    }

} catch (Exception $e) {
    echo "Ha ocurrido un error al procesar la solicitud. Intente más tarde.";
} finally {
    $pdo       = null;
    $clasePDO  = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <!-- jQuery y plugin de validación -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.21.0/dist/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="Estilos/estilos.css">
</head>
<body>
<div class="form-container">
    <h1>Registro de Usuario</h1>

    <span id="mensaje-estado"></span>

    <form id="form1" method="POST" action="registrese_form.php">
        <input type="hidden" name="Accion" value="Guardar">

        <fieldset>
            <legend>Nombre:</legend>
            <input type="text" name="nombre" id="nombre" placeholder="Ej: Juan">
        </fieldset>

        <fieldset>
            <legend>Apellido:</legend>
            <input type="text" name="apellido" id="apellido" placeholder="Ej: Pérez">
        </fieldset>

        <fieldset>
            <legend>Usuario:</legend>
            <input type="text" name="usuario" id="usuario" placeholder="Ej: jperez">
        </fieldset>

        <fieldset>
            <legend>Email personal:</legend>
            <input type="email" name="email1" id="email1" 
                   placeholder="Ej: correo@ejemplo.com">
            <span id="mensaje-correo" style="font-size:13px;"></span>
        </fieldset>

        <fieldset>
            <legend>Contraseña:</legend>
            <input type="password" name="clave" id="clave" placeholder="Mínimo 8 caracteres">
        </fieldset>

        <fieldset>
            <legend>Repetir Contraseña:</legend>
            <input type="password" name="clave_again" id="clave_again" 
                   placeholder="Repite tu contraseña">
        </fieldset>

        <button type="submit" class="btn-submit">Registrarse</button>
    </form>

    <div class="link-login">
        ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
    </div>
</div>

<script>
$(document).ready(function () {

    $("#form1").validate({
        rules: {
            nombre:     { required: true, minlength: 2 },
            apellido:   { required: true, minlength: 2 },
            usuario:    { required: true, minlength: 3 },
            clave:      { required: true, minlength: 8 },
            clave_again:{ required: true, equalTo: "#clave" },
            email1:     { required: true, email: true }
        },
        messages: {
            nombre:     { required: "El nombre es obligatorio", minlength: "Mínimo 2 caracteres" },
            apellido:   { required: "El apellido es obligatorio", minlength: "Mínimo 2 caracteres" },
            usuario:    { required: "El usuario es obligatorio", minlength: "Mínimo 3 caracteres" },
            clave:      { required: "La contraseña es obligatoria", minlength: "Mínimo 8 caracteres" },
            clave_again:{ required: "Repite tu contraseña", equalTo: "Las contraseñas no coinciden" },
            email1:     { required: "El correo es obligatorio", email: "Formato de correo inválido" }
        },
        submitHandler: function(form1) {

            var email1 = $("#email1").val();

            // Verificar correo duplicado antes de enviar
            $.ajax({
                type: "POST",
                url:  "clases/verificarCorreo.php",
                data: { email: email1 },
                dataType: "html",
                beforeSend: function() {
                    $("#mensaje-correo").html("Verificando correo...");
                },
                success: function(datos) {
                    datos = $.trim(datos);
                    if (datos === "libre") {
                        $("#mensaje-correo").html("");
                        form1.submit();
                    } else {
                        $("#mensaje-correo").html(
                            "<span style='color:red;'>Este correo ya está en uso.</span>"
                        );
                    }
                },
                error: function() {
                    alert("El proceso ha fallado. Intente de nuevo.");
                }
            });
        }
    });

});
</script>
</body>
</html>