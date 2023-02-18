<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mcamara extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getFotos($idhojapruebas) {
        $rta = $this->db->query("SELECT 
                                'cam' tipo_consulta,p.* 
                                FROM 
                                pruebas p 
                                WHERE 
                                p.estado=0 and
                                p.idtipo_prueba=5 and
                                p.idhojapruebas=$idhojapruebas LIMIT 2;");
        $r = $rta->result();
        return $r[0];
    }

    function getHojaPruebas($idprueba) {
        $rta = $this->db->query("SELECT 
                'setHojaPruebas' funcionSet,h.* 
                FROM 
                pruebas p,hojatrabajo h 
                WHERE 
                h.idhojapruebas=p.idhojapruebas AND 
                p.idprueba=$idprueba");
        $r = $rta->result();
        return $r[0];
    }

    function getFoto($idhojapruebas, $order, $prueba) {
        $rta = $this->db->query("SELECT 
                'setFoto' funcionSet,p.idprueba,
                IFNULL((SELECT i.imagen FROM imagenes i WHERE i.idprueba=p.idprueba LIMIT 1) ,'') foto,p.prueba
                FROM 
                pruebas p,hojatrabajo h
                WHERE
                p.idhojapruebas=h.idhojapruebas AND
                p.estado<>3 and
                p.estado<>9 and
                p.idtipo_prueba=5 AND
                p.prueba=$prueba AND
                h.idhojapruebas=$idhojapruebas
                ORDER BY 1 $order LIMIT 1");
        $r = $rta->result();
        return $r[0];
    }

    function guardarImagen($idprueba, $imagen, $idusuario, $idmaquina) {
        $this->db->trans_start();
        $img = $this->buscarImagen($idprueba);
        if ($img->num_rows > 0) {
            $this->db->set('imagen', $imagen, FALSE);
            $this->db->where('idprueba', $idprueba);
            $this->db->update("imagenes");
        } else {
            $data['idprueba'] = $idprueba;
            $data['imagen'] = $imagen;
            $this->db->insert("imagenes", $data);
        }
        $this->db->trans_complete();
        return $this->updatePrueba($idprueba, $idusuario, $idmaquina);
    }

    function buscarImagen($idprueba) {
        return $this->db->query("select *
                FROM
                imagenes i
                where
                i.idprueba=$idprueba");
    }

    function updatePrueba($idprueba, $idusuario, $idmaquina) {
        $this->db->set("fechainicial", "fechainicial", FALSE);
        $this->db->set("fechafinal", "now()", FALSE);
        $this->db->set("idusuario", $idusuario, FALSE);
        $this->db->set("estado", "2", FALSE);
        $this->db->set("idmaquina", $idmaquina, FALSE);
        $this->db->where('idprueba', $idprueba);
        return $this->db->update("pruebas");
    }

}
