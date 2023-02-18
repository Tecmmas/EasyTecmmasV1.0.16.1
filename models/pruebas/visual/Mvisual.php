<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mvisual extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getDefectos($idprueba) {
        $rta = $this->db->query("SELECT 
                                'setDefectos' funcionSet,r.* 
                                FROM 
                                resultados r 
                                WHERE 
                                r.idprueba=$idprueba;");
        return $rta;
    }

    function getObservaciones($codigo) {
        $rta = $this->db->query("SELECT 
                                'setObservaciones' funcionSet,o.* 
                                FROM 
                                observaciones o 
                                WHERE 
                                o.codigo='$codigo';");
        return $rta;
    }

    function insertarDefecto($idprueba, $valor) {
        $data['tiporesultado'] = 'defecto';
        $data['idprueba'] = $idprueba;
        $data['valor'] = $valor;
        $data['observacion'] = "";
        $data['idconfig_prueba'] = "153";
        $this->borrarDefecto($idprueba, $valor);
        echo $this->db->insert('resultados', $data);
    }

    function borrarDefecto($idprueba, $valor) {
//        $data['tiporesultado'] = 'defecto';
//        $data['idprueba'] = $idprueba;
//        $data['valor'] = $valor;
//        $data['observacion'] = "";
//        $data['idconfig_prueba'] = "153";
        $this->db->where('idprueba', $idprueba);
        $this->db->where('valor', $valor);
        echo $this->db->delete('resultados');
//        echo $this->db->insert('resultados', $data);
    }

    function getObses($idprueba) {
        $rta = $this->db->query("SELECT 
                                'setObses' funcionSet,r.* 
                                FROM 
                                resultados r 
                                WHERE 
                                r.idprueba=$idprueba and idconfig_prueba=77;");
        return $rta;
    }

    function insertarObse($data) {
        echo $this->db->insert('resultados', $data);
    }

    function borrarObse($data) {
        $this->db->where('idprueba', $data['idprueba']);
        $this->db->where('tiporesultado', $data['tiporesultado']);
        $this->db->where('idconfig_prueba', '77');
        echo $this->db->delete('resultados');
    }

    function insertarObservacion($codigo, $observacion) {
        $data['codigo'] = $codigo;
        $data['observacion'] = $observacion;
        echo $this->db->insert('observaciones', $data);
    }

    function borrarObservacion($codigo, $observacion) {
        $this->db->where('codigo', $codigo);
        $this->db->where('observacion', $observacion);
        echo $this->db->delete('observaciones');
    }

    function actualizarObservacion($idprueba, $codigo, $observacion) {
        $this->db->set('observacion', $observacion);
        $this->db->where('idprueba', $idprueba);
        $this->db->where('valor', $codigo);
        echo $this->db->update('resultados');
    }

    function insertarLabrado($idprueba, $tiporesultado, $valor) {
        $data['tiporesultado'] = $tiporesultado;
        $data['idprueba'] = $idprueba;
        $data['valor'] = str_replace(",", ".", $valor);
        $data['observacion'] = "OBSERVACIONLABRADO";
        $data['idconfig_prueba'] = "96";
        echo $this->db->insert('resultados', $data);
    }

    function insertarObservacionesAdd($idprueba, $valor) {
        $this->db->where('idprueba', $idprueba);
        $this->db->where('tiporesultado', 'COMENTARIOSADICIONALES');
        $this->db->delete('resultados');
        $data['tiporesultado'] = 'COMENTARIOSADICIONALES';
        $data['idprueba'] = $idprueba;
        $data['valor'] = $valor;
        $data['observacion'] = "";
        $data['idconfig_prueba'] = "153";
        echo $this->db->insert('resultados', $data);
    }

    function borrarLabrado($idprueba, $tiporesultado) {
        $this->db->where('idprueba', $idprueba);
        $this->db->where('tiporesultado', $tiporesultado);
        echo $this->db->delete('resultados');
    }

    function getLabrados($idprueba) {
        $rta = $this->db->query("SELECT 
                                'setLabrados' funcionSet,r.* 
                                FROM 
                                resultados r 
                                WHERE 
                                r.idprueba=$idprueba and 
                                r.observacion='OBSERVACIONLABRADO';");
        return $rta;
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

    function getPruebasAsignadas($idhojapruebas, $reinspeccion) {
        $orderBy = 'DESC';
        if ($reinspeccion == '0' || $reinspeccion == '4444' || $reinspeccion == '8888') {
            $orderBy = '';
        }
        $fechaInicial = $this->getFechaInicial($idhojapruebas, $reinspeccion);
        $rta = $this->db->query("SELECT 
                p.* 
                FROM 
                pruebas p
                WHERE 
                p.idhojapruebas=$idhojapruebas and
                (p.estado<>5 and p.estado<>3) and
                (p.fechainicial='$fechaInicial' or  p.fechainicial between '$fechaInicial' and DATE_ADD('$fechaInicial',INTERVAL 120 MINUTE))
                GROUP BY p.prueba,p.idtipo_prueba
                ORDER BY 1 $orderBy");
        return $rta;
    }

    function getFechaInicial($idhojapruebas, $reins) {
        $l = '';
        if ($reins == '1' || $reins == '44441') {
            $l = ',1';
        }
        $query = $this->db->query("
            SELECT  
                p.fechainicial,COUNT(*) c
            FROM 
            pruebas p 
            WHERE 
            p.idhojapruebas=$idhojapruebas
            GROUP BY 1 ORDER BY 2 DESC LIMIT 1$l");
        $r = $query->result();
        return $r[0]->fechainicial;
    }

    function actualizarPruebaXMaq($idprueba, $idmaquina, $idusuario) {
        $this->db->set('idmaquina', $idmaquina, false);
        $this->db->set('fechainicial', 'fechainicial', false);
        $tipoPrueba = $this->getTipoPrueba($idprueba);
        if ($tipoPrueba == '12' ||
                $tipoPrueba == '13' ||
                $tipoPrueba == '14' ||
                $tipoPrueba == '15' ||
                $tipoPrueba == '16' ||
                $tipoPrueba == '17' ||
                $tipoPrueba == '18' ||
                $tipoPrueba == '19') {
            $this->db->set('estado', '2', false);
            $this->db->set('idusuario', $idusuario, false);
            $this->db->set('fechafinal', 'now()', false);
        }
        $this->db->where('idprueba', $idprueba);
        echo $this->db->update('pruebas');
    }

    function getTipoPrueba($idprueba) {
        $rta = $this->db->query("SELECT p.idtipo_prueba FROM pruebas p WHERE p.idprueba=$idprueba");
        $r = $rta->result();
        return $r[0]->idtipo_prueba;
    }

}
