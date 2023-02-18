<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MPrueba extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function guardarResultado($resultados) {
        echo $this->db->insert('resultados', $resultados);
    }

    function actualizarPrueba($pruebas) {
        $this->db->where('idprueba', $pruebas['idprueba']);
        echo $this->db->update('pruebas', $pruebas);
    }

    function guardarAuditoria($auditoria_sicov) {
        echo $this->db->insert('auditoria_sicov', $auditoria_sicov);
    }

    function getHojaPruebas($idHojaPruebas) {
        $this->db->where('idhojapruebas', $idHojaPruebas);
        $query = $this->db->get('hojatrabajo', 1);
        return $query->result();
    }

    function getPruebas($idPrueba) {
        $this->db->where('idprueba', $idPrueba);
        $query = $this->db->get('pruebas', 1);
        return $query->result();
    }

}
