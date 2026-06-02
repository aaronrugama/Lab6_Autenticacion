<?php

/**
 * Redirige al usuario a una página específica
 */
function redireccionar($pagina) {
    header("Location: " . $pagina);
    exit();
}

/**
 * Verifica que el usuario esté autenticado.
 * Si no tiene sesión activa, lo manda al login.
 * 
 * Se llama al inicio de cualquier página protegida.
 */
function bloqueSeguridadSesion() {
    session_start();

    if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== "SI") {
        // Libera la variable de sesión del usuario
        unset($_SESSION['Usuario']);
        // Elimina toda la sesión
        session_destroy();
        // Manda al login
        redireccionar("../login.php");
    }
}

/**
 * Evita valores nulos o undefined en variables de sesión
 * Retorna el valor si existe, o el valor por defecto si no
 */
function nvl(&$var, $default = "") {
    return isset($var) ? $var : $default;
}

/**
 * Registra un intento de login en la tabla intentos_login
 * Detecta anomalía si hay más de 5 intentos fallidos desde la misma IP
 */
function registrarIntentoLogin($pdo, $usuario, $ipRemoto, $exitoso) {

    // Verificar si hay más de 5 intentos fallidos recientes desde esta IP
    $sql = "SELECT COUNT(*) as total FROM intentos_login 
            WHERE ipRemoto = :ip 
            AND deteccion_anomalia = 0
            AND timestamp >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":ip", $ipRemoto);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si hay 5 o más intentos fallidos recientes, marcamos anomalía
    $anomalia = ($resultado['total'] >= 5) ? 1 : 0;

    // Si el intento es exitoso no es anomalía
    if ($exitoso) {
        $anomalia = 0;
    }

    // Registrar el intento
    $data = [
        "usuario"          => $usuario,
        "ipRemoto"         => $ipRemoto,
        "deteccion_anomalia" => $exitoso ? 0 : $anomalia,
        "timestamp"        => date("Y-m-d H:i:s")
    ];

    $columnas   = implode(", ", array_keys($data));
    $parametros = ":" . implode(", :", array_keys($data));
    $sqlInsert  = "INSERT INTO intentos_login ($columnas) VALUES ($parametros)";

    $stmtInsert = $pdo->prepare($sqlInsert);
    foreach ($data as $key => $value) {
        $stmtInsert->bindValue(":$key", $value);
    }
    $stmtInsert->execute();
}