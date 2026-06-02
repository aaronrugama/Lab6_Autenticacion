<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);

include("mysql.inc.php");
include("SanitizarEntrada.php");

try {

    $clasePDO = new mod_db();
    $pdo      = $clasePDO->getConexion();

    // Sanitizar el correo recibido
    $email = SanitizarEntrada::limpiarEspacios($_POST['email'] ?? '');

    if (empty($email) || !SanitizarEntrada::validarCorreo($email)) {
        echo "invalido";
        exit();
    }

    // Consultar si el correo ya existe
    $query = $pdo->prepare("SELECT * FROM usuarios WHERE Correo = :email");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();

    $resultado = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) >= 1) {
        echo "existe";
    } else {
        echo "libre";
    }

} catch (PDOException $e) {
    echo "error";
} finally {
    $pdo      = null;
    $clasePDO = null;
}