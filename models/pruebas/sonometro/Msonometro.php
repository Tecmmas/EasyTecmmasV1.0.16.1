<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Msonometro extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getSonometro_ht_fecha($idprueba) {
        $rta = $this->db->query("SELECT 
                                'son' tipo_consulta,p.* 
                                FROM 
                                pruebas p 
                                WHERE 
                                (SELECT 
                                idhojapruebas 
                                FROM 
                                pruebas 
                                WHERE 
                                idprueba=$idprueba LIMIT 1)=p.idhojapruebas  AND
                                p.idtipo_prueba=4  and
                                p.estado=0
                                LIMIT 1");
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
            return $r[0];
        } else {
            return (object) array('tipo_consulta' => 'son');
        }
    }

    function getSonometria($idprueba) {
        $rta = $this->db->query("SELECT 
                                'son' tipo_consulta,p.* 
                                FROM 
                                pruebas p 
                                WHERE 
                                p.idprueba=$idprueba LIMIT 1;");
        $r = $rta->result();
        return $r[0];
    }

}
