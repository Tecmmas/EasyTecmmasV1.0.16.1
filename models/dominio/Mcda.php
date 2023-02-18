<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mcda extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get() {
        $query = $this->db->get('cda');
        return $query;
    }
}
