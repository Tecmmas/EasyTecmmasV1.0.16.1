<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MPruebas extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function consultarPlaca($placa) {
        $query = <<<EOF
            SELECT 
            v.numero_placa,
            tp.nombre tipo_vehiculo,
            v.tipo_vehiculo idtipo_vehiculo,
            tc.idtipocombustible,
            tc.nombre tipo_combustible,
            cla.nombre clase,
            v.idclase,
            v.idservicio,
            ifnull(
            (SELECT 
            h.idhojapruebas
            FROM 
            hojatrabajo h
            WHERE 
            h.estadototal=7 AND
            h.reinspeccion = 0 AND
            v.idvehiculo=h.idvehiculo AND
            v.numero_placa = '$placa' AND
            TIMESTAMPDIFF(minute,h.fechainicial,NOW())<=22320 LIMIT 1)
            ,IFNULL((SELECT 
            '2'
            FROM 
            hojatrabajo h
            WHERE 
            h.estadototal=1 AND
            (h.reinspeccion = 0 or h.reinspeccion = 1) AND
            v.idvehiculo=h.idvehiculo AND
            v.numero_placa = '$placa' AND
            DATEDIFF(NOW(),h.fechainicial)=0  LIMIT 1)
            ,'0')) RTEMecReins,
            ifnull(
            (SELECT 
            h.idhojapruebas
            FROM 
            hojatrabajo h
            WHERE 
            h.estadototal=3 AND
            h.reinspeccion = 4444 AND
            v.idvehiculo=h.idvehiculo AND
            v.numero_placa = '$placa' AND
            TIMESTAMPDIFF(minute,h.fechainicial,NOW())<=22320 LIMIT 1)
            ,IFNULL((SELECT 
            '2'
            FROM 
            hojatrabajo h
            WHERE 
            h.estadototal=1 AND
            (h.reinspeccion = 4444 or h.reinspeccion = 44441) AND
            v.idvehiculo=h.idvehiculo AND
            v.numero_placa = '$placa' AND
            DATEDIFF(NOW(),h.fechainicial)=0  LIMIT 1)
            ,'0')) PreventivaReins,
            v.taximetro,
            v.idvehiculo,
            if(v.registrorunt='0',(select l.nombre from linea l where l.idlinea=v.idlinea limit 1),(select l.nombre from linearunt l where l.idlinearunt=v.idlinea limit 1)) linea,
            if(v.registrorunt='0',(select m.nombre from linea l,marca m where l.idlinea=v.idlinea and l.idmarca=m.idmarca limit 1),(select m.nombre from linearunt l,marcarunt m where l.idlinearunt=v.idlinea and m.idmarcarunt=l.idmarcarunt limit 1)) marca,
            v.ano_modelo,
            if(v.registrorunt='0',(select co.nombre from color co where co.idcolor=v.idcolor limit 1),(select co.nombre from colorrunt co where co.idcolorrunt=v.idcolor limit 1)) color
            FROM
            vehiculos v,tipo_vehiculo tp,clase cla,tipo_combustible tc
            WHERE 
            v.numero_placa = '$placa' AND
            tp.idtipo_vehiculo=v.tipo_vehiculo AND
            cla.idclase=v.idclase and
            tc.idtipocombustible=v.idtipocombustible
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    function getUltimaFactura() {
        $query = <<<EOF
            select factura 
            from hojatrabajo 
            where factura<>'0' and 
                  factura<>'' and 
                  factura REGEXP '^[0-9]+$' 
                  order by idhojapruebas desc limit 1
EOF;
        $rta = $this->db->query($query);
        $r = $rta->result();
        return $r[0]->factura;
    }

    function validarPrerevision($numero_placa) {
        $query = <<<EOF
            SELECT
            * 
            FROM 
                pre_prerevision pp 
            WHERE
                pp.numero_placa_ref = '$numero_placa' AND
                DATE_FORMAT(pp.fecha_prerevision,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') AND
                pp.tipo_inspeccion=1
EOF;
        $rta = $this->db->query($query);
        return $rta->num_rows();
    }

    function validarFactura($noFactura) {
        $query = <<<EOF
            select *
            from hojatrabajo 
            where factura='$noFactura'
EOF;
        $rta = $this->db->query($query);
        return $rta->num_rows();
    }

    function getPruebas($idhojaprueba) {
        $query = <<<EOF
            SELECT 
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=1 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") luxometro,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=2 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") opacidad,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=3 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") gases,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=6 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") taximetro,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=7 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") frenometro,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=9 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") suspension,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=10 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") alineacion,
                IFNULL((SELECT p.estado FROM pruebas p WHERE p.idhojapruebas=$idhojaprueba AND p.idtipo_prueba=4 AND p.estado<>5 AND p.estado<>9 ORDER BY 1 DESC LIMIT 1),"3") sonometro,
                IFNULL((SELECT h.pin0 FROM hojatrabajo h WHERE h.idhojapruebas=$idhojaprueba AND h.estadototal<>5 ORDER BY 1 DESC LIMIT 1),"") pin
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

}
