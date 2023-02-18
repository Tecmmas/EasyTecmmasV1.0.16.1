<?php

class PruebasModel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }
   

    public function get($estado, $idtipo_prueba, $idhojapruebas) {
        $data = $this->db->query("
            SELECT 
                p.idprueba
            FROM 
                pruebas p
            WHERE 
                p.estado=$estado and
                p.idtipo_prueba=$idtipo_prueba and
                p.idhojapruebas=$idhojapruebas
            ORDER BY
                1 desc
            LIMIT
                1");
        if ($data->num_rows() > 0) {
            return $data;
        } else {
            return FALSE;
        }
    }

    public function update($data) {
        $this->db->set('fechafinal', 'NOW()', FALSE);
        $this->db->set('fechainicial', 'fechainicial', FALSE);
        $this->db->where('idprueba', $data['idprueba']);
        $this->db->update('pruebas', $data);
    }

}
