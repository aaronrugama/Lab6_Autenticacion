<?php

include("comunes/loginfunciones.php");

session_start();

// Liberar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión completamente
session_destroy();

// Redirigir al login
redireccionar("login.php");