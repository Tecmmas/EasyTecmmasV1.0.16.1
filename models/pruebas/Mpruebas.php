<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mpruebas extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getPruebas($idtipo_prueba, $reinspeccion, $tipo_vehiculo) {
        if ($idtipo_prueba == 7 || $idtipo_prueba == 9 || $idtipo_prueba == 10) {
            $idtipo_prueba = "(p.idtipo_prueba=7 || p.idtipo_prueba=9 || p.idtipo_prueba=10) and";
            $group = "group by v.numero_placa";
        } else {
            $idtipo_prueba = "p.idtipo_prueba=$idtipo_prueba and";
            $group = "group by p.idhojapruebas,p.idtipo_prueba";
        }
        $result = $this->db->query("
            select 
            p.idprueba,v.numero_placa,h.reinspeccion,v.idclase,v.tipo_vehiculo,v.taximetro,v.idservicio,v.tiempos
            from 
            pruebas p, hojatrabajo h,vehiculos v 
            where 
            p.idhojapruebas=h.idhojapruebas and
            v.idvehiculo=h.idvehiculo and
            $idtipo_prueba
            $reinspeccion
            $tipo_vehiculo
            p.estado=0 and h.estadototal<>5 and date_format(p.fechainicial,'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')
            $group;");
        return $result;
    }

    function insertarResultado($resultados) {
        $this->db->trans_start();
        $rta = $this->db->insert("resultados", $resultados);
        if ($rta) {
            return 1;
        } else {
            return 0;
        }
        $this->db->trans_complete();
    }

    function insertarPrueba($prueba) {
        echo $this->db->insert("pruebas", $prueba);
    }

    function insertarPruebaExosto($prueba) {
        $this->db->insert("pruebas", $prueba);
        return $this->db->insert_id();
    }

    function actualizarPruebas($pruebas) {
        $this->db->trans_start();
//        $
//        $this->db->set('fechafinal', 'NOW()', FALSE);
//        $this->db->set('idusuario', $pruebas['idusuario']);
//        $this->db->set('idmaquina', $pruebas['idmaquina']);
////        $this->db->set('estado', $pruebas['estado']);
//        $this->db->set('estado', "hola");
//        $this->db->set('fechainicial', 'fechainicial', FALSE);
//        $this->db->where('idprueba', $pruebas['idprueba']);
//        $rta = $this->db->update("pruebas", $pruebas);
        //echo $data = "UPDATE pruebas SET fechainicial=fechainicial, estado=" . $pruebas['estado'] . ", idusuario =" . $pruebas['idusuario'] . ",  "
          //      . "idmaquina =" . $pruebas['idmaquina'] . ",fechafinal = now()  WHERE idprueba=" . $pruebas['idprueba'];
        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial, estado=" . $pruebas['estado'] . ", idusuario =" . $pruebas['idusuario'] . ",  "
                . "idmaquina =" . $pruebas['idmaquina'] . ",fechafinal = now()  WHERE idprueba=" . $pruebas['idprueba']);
        //echo "que: " . $query;
        $this->db->trans_complete();
        if($query){
            return 1;
        }else{
            return 0;
        }
        
    }
    
    function eliminarResultados($idprueba){
        $query = $this->db->query("DELETE FROM resultados  WHERE idprueba=$idprueba;");
    }

    function actualizarPruebasExosto($pruebas) {
        $this->db->set('fechafinal', 'NOW()', FALSE);
        $this->db->set('idusuario', $pruebas['idusuario']);
        $this->db->set('prueba', $pruebas['prueba']);
        $this->db->set('idmaquina', $pruebas['idmaquina']);
        $this->db->set('estado', $pruebas['estado']);
        $this->db->set('fechainicial', 'fechainicial', FALSE);
        $this->db->where('idprueba', $pruebas['idprueba']);
        echo $this->db->update("pruebas", $pruebas);
    }

    function obtenerPlacaIdprueba($idprueba) {
        $result = $this->db->query("select 
                        v.numero_placa
                        from 
                        vehiculos v,pruebas p, hojatrabajo h 
                        where
                        v.idvehiculo=h.idvehiculo and
                        p.idhojapruebas=h.idhojapruebas and
                        p.idprueba=$idprueba limit 1");
        $rta = $result->result();
        return $rta[0]->numero_placa;
    }

    function getMaquina($serie, $idtipo_prueba) {
        echo $serie;
        echo $idtipo_prueba;
        $this->db->where('serie', $serie);
        $this->db->where('idtipo_prueba', $idtipo_prueba);
        $query = $this->db->get('maquina');
        $rta = $query->result();
        return $rta[0];
    }

    function getUsuario($idusuario) {
        $this->db->trans_start();
        $this->db->where('IdUsuario', $idusuario);
        $query = $this->db->get('usuarios');
        $rta = $query->result();
        return $rta[0];
        $this->db->trans_complete();
    }

    function getUsuarioClave($passwd) {
        $where = "passwd='$passwd' AND (idperfil='1' OR idperfil='3') and estado='1'";
        $this->db->where($where);
        $query = $this->db->get('usuarios');
        return $query->num_rows();
    }

    function getHojaPruebas($idprueba) {
        $this->db->trans_start();
        $result = $this->db->query("select 
                h.* 
                from 
                pruebas p,hojatrabajo h 
                where
                p.idhojapruebas=h.idhojapruebas and
                p.idprueba=$idprueba limit 1;");
        $rta = $result->result();
        return $rta[0];
        $this->db->trans_complete();
    }

    function getSicovRunt() {
        $result = $this->db->query("select valor,adicional from config_prueba where idconfig_prueba=502");
        $rta = $result->result();
        return $rta[0];
    }

    function buscarResultado($idprueba, $idconfig_prueba) {
        $result = $this->db->query("select valor from resultados where idprueba=$idprueba and idconfig_prueba=$idconfig_prueba limit 1");
        $rta = $result->result();
        $valor = "";
        if ($result->num_rows() > 0) {
            $valor = $rta[0]->valor;
        }
        return $valor;
    }

    function insertarAuditoriaSicov($auditoria_sicov) {
        echo $this->db->insert("auditoria_sicov", $auditoria_sicov);
    }

}
