<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mth extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get($idmaquina) {
        $rta = $this->db->query("SELECT 
                                    c.tipo_parametro,parametro
                                    FROM
                                    config_maquina c
                                    WHERE
                                    c.idmaquina=$idmaquina;");
        return $rta;
    }
    function getDiff($idmaquina) {
        $rta = $this->db->query("SELECT 
                                    TIMESTAMPDIFF(SECOND,STR_TO_DATE(c.parametro,'%d/%m/%Y %H:%i:%s'),NOW()) diferencia 
                                    FROM
                                    config_maquina c
                                    WHERE
                                    c.tipo_parametro='Last Update' and
                                    c.idmaquina=$idmaquina limit 1;");
        return $rta;
    }

}
