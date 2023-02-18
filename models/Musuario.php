<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Musuario extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getUsuario($usuario, $contrasena) {
        $query = $this->db->query("SELECT if(u.fecha_actualizacion < CURDATE(), 'AC','') AS 'fecha',
                                        u.*,(SELECT c.nombre_cda FROM cda c LIMIT 1) cda
                                    FROM 
                                        usuarios u
                                    WHERE
                                        u.username='$usuario' and u.passwd='$contrasena'");
        return $query;

//        $this->db->where('username', $usuario);
//        $this->db->where('passwd', $contrasena);
//        $query = $this->db->get('usuarios');
//        return $query;
    }

    function getUsuarioId($id) {
        $this->db->where('IdUsuario', $id);
        $query = $this->db->get('usuarios');
        return $query;
    }

    function getOpciones($id) {
        $this->db->where('idusuario', $id);
        $query = $this->db->get('usr_opcion');
        return $query;
    }

    function actualizarOpcion($opcion) {
        if (!$this->buscarOpcion($opcion)) {
            return $this->db->insert('usr_opcion', $opcion);
        } else {
            $this->db->where('idusuario', $opcion['idusuario']);
            $this->db->where('nombre', $opcion['nombre']);
            return $this->db->update('usr_opcion', $opcion);
        }
    }

    function buscarOpcion($opcion) {
        $this->db->where('idusuario', $opcion['idusuario']);
        $this->db->where('nombre', $opcion['nombre']);
        $query = $this->db->get('usr_opcion');
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return FALSE;
        }
    }

    private function BuscarTablaOpciones() {
        $data = $this->db->query("
            show tables like '%usr_opcion%' ");
        if ($data->num_rows() > 0) {
            return $data;
        } else {
            return FALSE;
        }
    }

    function CrearTablaOpciones() {
        if (!$this->BuscarTablaOpciones()) {
            try {
                $query = "CREATE TABLE `usr_opcion` (
                            `idusr_opcion` INT(11) NOT NULL AUTO_INCREMENT,
                            `idusuario` INT(11) NOT NULL,
                            `nombre` VARCHAR(45) NOT NULL,
                            `valor` VARCHAR(45) NOT NULL,
                            PRIMARY KEY (`idusr_opcion`))
                          ENGINE = MyISAM;";
                $this->db->query($query);
            } catch (Exception $ex) {
                echo($ex->getMessage());
                return false;
            }
        }
    }

}
