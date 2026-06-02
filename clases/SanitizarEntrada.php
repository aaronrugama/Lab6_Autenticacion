<?php

class SanitizarEntrada {

    /**
     * Limpia texto general: elimina espacios extra y caracteres peligrosos
     */
    public static function limpiarCadena($valor) {
        $valor = trim($valor);
        $valor = stripslashes($valor);
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        return $valor;
    }

    /**
     * Para nombres y apellidos: solo letras, espacios y tildes
     */
    public static function CadTitulo($valor) {
        $valor = trim($valor);
        $valor = stripslashes($valor);
        $valor = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        // Solo permite letras (incluye tildes y ñ), espacios y guiones
        $valor = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]/u', '', $valor);
        return $valor;
    }

    /**
     * Para correos electrónicos
     */
    public static function limpiarEspacios($valor) {
        $valor = trim($valor);
        $valor = stripslashes($valor);
        $valor = filter_var($valor, FILTER_SANITIZE_EMAIL);
        return $valor;
    }

    /**
     * Valida que un correo tenga formato correcto
     */
    public static function validarCorreo($valor) {
        return filter_var($valor, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Para contraseñas: solo elimina espacios al inicio y al final
     * NO se escapan caracteres porque se va a hashear
     */
    public static function limpiarPassword($valor) {
        return trim($valor);
    }

} // fin clase SanitizarEntrada