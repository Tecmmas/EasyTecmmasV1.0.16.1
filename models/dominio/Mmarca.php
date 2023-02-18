<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mmarca extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get($data) {
        $this->db->where("idmarcaRUNT", $data['idmarcaRUNT']);
        $query = $this->db->get('marcaRUNT');
        return $query;
    }

    function get2($data) {
        $this->db->where("idmarca", $data['idmarca']);
        $query = $this->db->get('marca');
        return $query;
    }

}
