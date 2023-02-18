<?php

class LoginModel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function Ingresar($username, $passwd) {
        $data = $this->db->query("
            SELECT
                *
            FROM
                usuarios u
            WHERE
                u.username='$username' and u.passwd='$passwd'
            LIMIT
                1");
        if ($data->num_rows() > 0) {
            return $data;
        } else {
            return FALSE;
        }
    }

    public function getCda() {
        $data = $this->db->query("
            SELECT
                nombre_cda
            FROM
                cda
            LIMIT
                1");
        if ($data->num_rows() > 0) {
            $d = $data->result();
            return $d[0]->nombre_cda;
        } else {
            return FALSE;
        }
    }

    public function validarCronAudit() {
        $data = $this->db->query("
            show tables like '%cron_audit%' ");
        if ($data->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getTriggers() {
        $data = $this->db->query("SHOW TRIGGERS");
        return $data->result();
    }

}
