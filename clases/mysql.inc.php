<?php

class mod_db {

    private $conexion;
    private $debug = false;

    public function __construct() {

        ##### Setting SQL Vars #####
        $sql_host    = "localhost";
        $sql_name    = "company_info";
        $sql_user    = "aaron";       // NO usamos root
        $sql_pass    = "2trestrucos";    // cambia esto por tu contraseña
        $charset     = "utf8mb4";

        $dsn = "mysql:host=$sql_host;dbname=$sql_name;charset=$charset";

        try {
            $this->conexion = new PDO($dsn, $sql_user, $sql_pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            if ($this->debug) {
                echo "Conexión exitosa a la base de datos<br>";
            }

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function insertSeguro($tabla, $data) {
        $columnas  = implode(", ", array_keys($data));
        $parametros = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $tabla ($columnas) VALUES ($parametros)";

        try {
            $stmt = $this->conexion->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            echo "Error al insertar: " . $e->getMessage();
            return false;
        }
    }

    public function insert_id() {
        return $this->conexion->lastInsertId();
    }

    public function updateSeguro($tabla, $data, $condiciones) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $setSQL = implode(", ", $set);

        $where = [];
        foreach ($condiciones as $key => $value) {
            $where[] = "$key = :cond_$key";
        }
        $whereSQL = implode(" AND ", $where);

        $sql = "UPDATE $tabla SET $setSQL WHERE $whereSQL";

        try {
            $stmt = $this->conexion->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            foreach ($condiciones as $key => $value) {
                $stmt->bindValue(":cond_$key", $value);
            }

            return $stmt->execute();

        } catch (PDOException $e) {
            echo "Error en UPDATE: " . $e->getMessage();
            return false;
        }
    }

} // fin clase mod_db