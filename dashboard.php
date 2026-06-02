<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);

include("comunes/loginfunciones.php");

// Verifica que el usuario esté autenticado, si no lo manda al login
bloqueSeguridadSesion();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="Estilos/estilos.css">
</head>
<body class="dashboard">

<div class="navbar">
    <h2>LabLogin UTP</h2>
    <a href="salir.php">Cerrar Sesión</a>
</div>

<div class="contenido">

    <div class="card">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['Usuario']); ?>!</h1>
        <p>Has iniciado sesión correctamente con autenticación de dos factores (2FA).</p>
        <span class="badge-seguro">Sesión segura con 2FA activo</span>

        <div class="info-grid">
            <div class="info-item">
                <span>Usuario</span>
                <strong><?php echo htmlspecialchars($_SESSION['Usuario']); ?></strong>
            </div>
            <div class="info-item">
                <span>Correo</span>
                <strong><?php echo htmlspecialchars($_SESSION['Correo']); ?></strong>
            </div>
            <div class="info-item">
                <span>ID de sesión</span>
                <strong><?php echo session_id(); ?></strong>
            </div>
            <div class="info-item">
                <span>Autenticación</span>
                <strong>Usuario + Contraseña + 2FA</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <h1 style="font-size:18px;">📋 ¿Qué protege este sistema?</h1>
        <p>Este dashboard solo es accesible si:</p>
        <br>
        <p>✅ Ingresaste tu correo y contraseña correctamente.</p>
        <p>✅ Verificaste el código temporal de Google Authenticator.</p>
        <p>✅ Tu sesión está activa y no ha sido destruida.</p>
    </div>

</div>

</body>
</html>