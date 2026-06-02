<?php

class RegistroUsuario {

    // Atributos privados - solo accesibles desde esta clase
    private $id;
    private $Nombre;
    private $Apellido;
    private $Usuario;
    private $Correo;
    private $secret_2fa;
    private $contrasena;
    private $hastGenerado;
    private $pdo;
    private $tabla;
    private $FechaSistema;

    /**
     * Constructor: recibe los datos del formulario, el PDO y el array de mensajes de error
     * El & en $arrMensaje es paso por referencia, permite modificar el array original
     */
    public function __construct($datos, $pdo, &$arrMensaje) {

        $this->pdo          = $pdo;
        $this->tabla        = "usuarios";
        $this->FechaSistema = date("Y-m-d H:i:s");

        // Validar y sanitizar cada campo
        if (isset($datos["nombre"]) && $datos["nombre"] !== '') {
            $this->Nombre = SanitizarEntrada::CadTitulo($datos["nombre"]);
        } else {
            $arrMensaje[1] = "No trajo datos la Columna Nombre";
        }

        if (isset($datos["apellido"]) && $datos["apellido"] !== '') {
            $this->Apellido = SanitizarEntrada::CadTitulo($datos["apellido"]);
        } else {
            $arrMensaje[2] = "No trajo datos la Columna Apellido";
        }

        if (isset($datos["usuario"]) && $datos["usuario"] !== '') {
            $this->Usuario = SanitizarEntrada::limpiarCadena($datos["usuario"]);
        } else {
            $arrMensaje[3] = "No trajo datos la Columna Usuario";
        }

        if (isset($datos["email1"]) && $datos["email1"] !== '') {
            $correoLimpio = SanitizarEntrada::limpiarEspacios($datos["email1"]);
            if (SanitizarEntrada::validarCorreo($correoLimpio)) {
                $this->Correo = $correoLimpio;
            } else {
                $arrMensaje[4] = "El correo no tiene un formato válido";
            }
        } else {
            $arrMensaje[4] = "No trajo datos la Columna Correo";
        }

        if (isset($datos["clave"]) && $datos["clave"] !== '') {
            $this->contrasena = SanitizarEntrada::limpiarPassword($datos["clave"]);
        } else {
            $arrMensaje[5] = "No trajo datos la Columna Clave";
        }

    } // fin constructor

    /**
     * Encripta la contraseña usando bcrypt con costo 13
     */
    public function encriptarClave() {
        $options = [
            'cost' => 13,
        ];
        $this->hastGenerado = password_hash($this->contrasena, PASSWORD_BCRYPT, $options);
    }

    /**
     * Guarda el secreto 2FA generado por Google Authenticator en la BD
     */
    public function GuardarMySecreto($secreto) {
        $datoSecreto = array(
            "secret_2fa" => $secreto
        );
        $condicion = array(
            "id" => $this->id
        );

        if ($this->pdo->updateSeguro("usuarios", $datoSecreto, $condicion)) {
            return true;
        }
        return false;
    }

    /**
     * Guarda el usuario completo en la base de datos
     */
    public function Guardar_RegistroUsuario() {
        // Primero encriptamos la clave
        $this->encriptarClave();

        $data = array(
            "Nombre"       => $this->Nombre,
            "Apellido"     => $this->Apellido,
            "Usuario"      => $this->Usuario,
            "Correo"       => $this->Correo,
            "HashMagic"    => $this->hastGenerado,
            "secret_2fa"   => '',           // se llena después con el QR
            "FechaSistema" => $this->FechaSistema
        );

        $this->pdo->insertSeguro("usuarios", $data);
        $this->id = $this->pdo->insert_id();
    }

    /**
     * Getter del usuario (correo) para generar el QR
     */
    public function getUsuario() {
        return $this->Usuario;
    }

    /**
     * Getter del correo para generar el QR
     */
    public function getCorreo() {
        return $this->Correo;
    }

    /**
     * Getter del ID recién insertado
     */
    public function getId() {
        return $this->id;
    }

} // fin clase RegistroUsuario